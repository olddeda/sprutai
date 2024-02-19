<?php
namespace common\modules\content\models;

use common\modules\base\components\Debug;
use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\company\models\Company;

use common\modules\vote\models\Vote;

use common\modules\content\models\query\ContentCompanyStatQuery;

/**
 * This is the model class for table "{{%content_company_stat}}".
 *
 * @property int $id
 * @property int $company_id
 * @property int $articles
 * @property int $news
 * @property int $blogs
 * @property int $projects
 * @property int $plugins
 * @property int $subscribers
 * @property int $portfolios
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Company $company
 * @property Content $content
 */
class ContentCompanyStat extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_company_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['company_id'], 'required'],
            [['company_id', 'articles', 'news', 'blogs', 'projects', 'plugins', 'subscribers', 'portfolios', 'created_at', 'updated_at'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'articles' => 'Articles',
            'news' => 'News',
            'blogs' => 'Blogs',
            'projects' => 'Projects',
            'plugins' => 'Plugins',
			'subscribers' => 'Subscribers',
			'portfolios' => 'Portfolios',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany() {
        return $this->hasOne(Company::class, ['id' => 'company_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\query\ContentCompanyStatQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentCompanyStatQuery(get_called_class());
    }
	
	/**
	 * Update links
	 * @param Company $company
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	static public function updateLinks(Company $company) {
		
		/** @var ContentCompanyStat $model */
		$model = $company->contentsStat;
		if (is_null($model)) {
			$model = new ContentCompanyStat();
			$model->company_id = $company->id;
		}
		
		$model->articles = count($company->contentsArticles);
		$model->news = count($company->contentsNews);
		$model->blogs = count($company->contentsBlogs);
		$model->projects = count($company->contentsProjects);
		$model->plugins = count($company->contentsPlugins);
		$model->portfolios = count($company->contentsPortfolios);
		$model->subscribers = Company::find()->select(' `companyFavoriteAggregate`.`positive`')->votes()->createCommand()->queryScalar();
		$model->save();
		
	}
}
