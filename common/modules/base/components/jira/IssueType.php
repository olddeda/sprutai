<?php
namespace common\modules\base\components\jira;

use yii\base\Model;

/**
 * Class IssueType
 * @package common\modules\base\components\jira
 *
 * @property Project $project
 */
class IssueType extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $subtask;

    /**
     * @var Project
     */
    protected $_project;
	
	/**
	 * @var array
	 */
    protected $_customFields;
	
	/**
	 * @var array
	 */
    protected $_customFieldsMap = [];


    /**
     * @param Project $project
     * @param array $data
     * @return IssueType
     */
    public static function populate(Project $project, $data) {
        if (!is_array($data) || !isset($data['id'])) {
            return null;
        }
        $issueType = new self;
        $issueType->_project = $project;
        $issueType->id = $data['id'];
        $issueType->name = $data['name'];
        $issueType->description = $data['description'];
        $issueType->subtask = $data['subtask'];

        return $issueType;
    }
	
	/**
	 * @param Project $project
	 * @param $data
	 *
	 * @return array
	 */
    public static function populateAll(Project $project, $data) {
        $issueTypes = [];
        foreach ($data as $row) {
            $issueTypes[$row['name']] = self::populate($project, $row);
        }

        return $issueTypes;
    }
	
	/**
	 * @return array
	 */
	public function getCustomFields() {
		if (isset($this->_customFields)) {
			return $this->_customFields;
		}
		$metaData = $this->project->client->get('issue/createmeta', [
			'expand' => 'projects.issuetypes.fields',
			'projectKeys' => $this->project->key,
		]);
		$this->_customFields = [];
		
		$issueTypes = $metaData['projects'][0]['issuetypes'];
		foreach ($issueTypes as $issueType) {
			if ($issueType['id'] != $this->id) {
				continue;
			}
			
			if (isset($issueType['fields'])) {
				$fields = $issueType['fields'];
				
				foreach ($fields as $name => $config) {
					if (strpos($name, 'customfield_') !== 0) {
						continue;
					}
					$id = substr($name, 12);
					$this->_customFields[$id] = $config;
					$this->_customFieldsMap[$config['name']] = $id;
				}
			}
			
		}
		
		return $this->_customFields;
	}
	
	/**
	 * @return array
	 */
    public function getCustomFieldsMap() {
        $this->getCustomFields();
        return $this->_customFieldsMap;
    }

    /**
     * @return Project
     */
    public function getProject() {
        return $this->_project;
    }

}
