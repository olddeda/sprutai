<?php
namespace common\modules\catalog\commands;

use Yii;
use yii\console\Controller;

use common\modules\notification\components\Notification;

use common\modules\catalog\models\CatalogItemOrder;
use common\modules\catalog\helpers\enum\StatusOrder;

use common\modules\base\extensions\sendgrid\Mailer;
use common\modules\base\extensions\sendgrid\Message;

use common\modules\hub\helpers\enum\LicenseType;
use common\modules\hub\models\HubLicense;

use Mailgun\Mailgun;
use yii\data\Sort;

/**
 * Class CliController
 * @package common\modules\catalog\commands
 */

class CliController extends Controller
{
    public function actionSendPreorder() {
        $limit = 85;
        $catalogItemId = 1809;

        $models = CatalogItemOrder::find()
            ->where([
                'status' => StatusOrder::PREORDER,
                'catalog_item_id' => $catalogItemId,
            ])
            ->limit($limit)
            ->orderBy(['created_at' => SORT_ASC])
            ->all();

        foreach($models as $model) {
            $model->status = StatusOrder::WAIT;
            $model->save();
        }
    }


    public function actionSendAddress() {
        $models = CatalogItemOrder::find()
            ->where([
                'status' => StatusOrder::WAIT,
            ])
            ->all();

        /** @var CatalogItemOrder $model */
        foreach ($models as $model) {

            $message = Yii::t('catalog-notification', 'order_address_request', [
                'id' => $model->id,
                'fio' => $model->fio,
                'phone' => $model->phone,
                'email' => $model->email,
                'catalog_item' => $model->catalogItem->title,
                'catalog_item_url' => 'https://v2.sprut.ai/catalog/item/'.$model->catalogItem->getSeo()->slugify,
                'status' => StatusOrder::getLabel($model->status),
                'url' => 'https://spruthub.ru/stick/order/'.$model->getHash(),
            ]);

            /** @var Notification $notification */
            $notification = Yii::$app->get('notification');
            $notification->queueEmails([
                $model->email
            ], Yii::t('catalog-notification', 'order_address_request_subject', [
                'id' => $model->id,
                'catalog_item' => $model->catalogItem->title,
            ]), $message, [
                'layouts' => [
                    'text' => '@common/modules/notification/tpl/layouts/text_empty',
                    'html' => '@common/modules/notification/tpl/layouts/html_empty',
                ],
            ]);

            $model->status = StatusOrder::ADDRESS;
            $model->save();
        }
    }

    public function actionFixLicense() {
        $models = CatalogItemOrder::find()
            ->where('license IS NULL')
            ->andWhere(['in', 'status', [StatusOrder::SENT, StatusOrder::COMPLETE]])
            ->andWhere(['in', 'catalog_item_id', [1809, 2305, 2570]])
            ->all()
        ;
        foreach ($models as $c) {
            $license = new HubLicense();
            $license->type = ($c->catalog_item_id == 2305) ? LicenseType::ZWAVE : LicenseType::ZIGBEE;
            $license->code = HubLicense::generateCode();
            $license->status = 1;
            if ($license->save()) {
                $c->license = $license->code;
                $c->save();
            }
        }
    }

    public function actionPreorder() {

        $nums = [];
        $n = 1;
        $models = CatalogItemOrder::find()
            ->with(['catalogItem'])
            ->andWhere(['status' => StatusOrder::PREORDER])
            ->orderBy(['id' => SORT_ASC])
            ->all()
        ;
        foreach ($models as $model) {
            $nums[$model->id] = $n++;
        }

        $query = CatalogItemOrder::find()
            ->with(['catalogItem'])
            ->andWhere(['status' => StatusOrder::PREORDER])
            ->andWhere('mailed_at IS NULL')
            ->orderBy(['id' => SORT_ASC])
        ;
        $count = $query->count();
        $models = $query->all();

        echo PHP_EOL;

        $n = 1;
        foreach ($models as $model) {
            $params = [
                'id' => $model->id,
                'fio' => $model->fio,
                'phone' => $model->phone,
                'email' => $model->email,
                'address' => $model->address,
                'catalog_item' => $model->catalogItem->title,
                'catalog_item_url' => 'https://v2.sprut.ai/catalog/item/'.$model->catalogItem->seo->slugify,
                'status' => StatusOrder::getLabel($model->status),
                'order_link' => 'https://spruthub.ru/stick/order/'.$model->getHash(),
                'num' => $nums[$model->id],
            ];

            $subject = Yii::t('catalog-notification', 'order_preorder_subject', $params);
            $message = Yii::t('catalog-notification', 'order_preorder_num', $params);

            /** @var \common\modules\base\extensions\mailgun\Mailer $mailer */
            $mailer = Yii::$app->mailerMailgun;
            $mailer->viewPath = '@common/modules/notification/tpl';
            $mailer->textLayout = '@common/modules/notification/tpl/layouts/text_empty';
            $mailer->htmlLayout = '@common/modules/notification/tpl/layouts/html_empty';

            $result = $mailer
                ->compose([
                    'text' => 'email-base.text.tpl.php',
                    'html' => 'email-base.html.tpl.php',
                ], [
                    'subject' => $subject,
                    'message' => $message,
                ])
                ->setFrom('Sprut.ai <noreply@sprut.ai>')
                ->setTo($model->email)
                ->setSubject($subject)
                ->send();

            if ($result) {
                $model->mailed_at = time();
                $model->save();
            }

            echo $n++.'/'.$count.': '.$model->id.' - '.($result ? 'Success' : 'Error').PHP_EOL;

            sleep(1);
        }

        echo PHP_EOL;
    }
}