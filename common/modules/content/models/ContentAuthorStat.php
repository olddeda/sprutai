<?php
namespace common\modules\content\models;

use Yii;

use common\modules\base\components\ActiveRecord;

use common\modules\user\models\User;

use common\modules\vote\models\Vote;

use common\modules\content\models\query\ContentAuthorStatQuery;

/**
 * This is the model class for table "{{%content_author_stat}}".
 *
 * @property int $id
 * @property int $author_id
 * @property int $articles
 * @property int $news
 * @property int $blogs
 * @property int $projects
 * @property int $plugins
 * @property int $subscribers
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $author
 * @property Content $content
 */
class ContentAuthorStat extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%content_author_stat}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['author_id'], 'required'],
            [['author_id', 'articles', 'news', 'blogs', 'projects', 'plugins', 'subscribers', 'created_at', 'updated_at'], 'integer'],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'articles' => 'Articles',
            'news' => 'News',
            'blogs' => 'Blogs',
            'projects' => 'Projects',
            'plugins' => 'Plugins',
			'subscribers' => 'Subscribers',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor() {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * {@inheritdoc}
     * @return \common\modules\content\models\query\ContentAuthorStatQuery the active query used by this AR class.
     */
    public static function find() {
        return new ContentAuthorStatQuery(get_called_class());
    }
	
	/**
	 * Update links
	 * @param User $user
	 *
	 * @throws \yii\base\InvalidConfigException
	 */
	static public function updateLinks(User $user) {
		$model = $user->contentsStat;
		if (is_null($model)) {
			$model = new ContentAuthorStat();
			$model->author_id = $user->id;
		}
		
		$model->articles = count($user->contentsArticles);
		$model->news = count($user->contentsNews);
		$model->blogs = count($user->contentsBlogs);
		$model->projects = count($user->contentsProjects);
		$model->plugins = count($user->contentsPlugins);
		$model->subscribers = User::find()->subscribers(Vote::USER_FAVORITE, $user->id)->count();
		$model->save();
		
	}
}
