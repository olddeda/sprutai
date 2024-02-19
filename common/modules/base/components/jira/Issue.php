<?php
namespace common\modules\base\components\jira;

use common\modules\base\components\Debug;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class Issue
 * @package common\modules\base\components\jira
 *
 * @property Project $project
 * @property string $key
 */
class Issue extends Model
{
	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	protected $_key;

	/**
	 * @var Project
	 */
	protected $_project;

	/**
	 * @var array
	 */
	protected $_issueLinksRaw;

	/**
	 * @var array
	 */
	protected $_issueLinks;

	/**
	 * @var User
	 */
	public $reporter;

	/**
	 * @var string
	 */
	public $summary;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var Status
	 */
	public $status;

	/**
	 * @var Priority
	 */
	public $priority;

	/**
	 * @var IssueType
	 */
	public $issueType;

	/**
	 * @var array
	 */
	public $components;

	/**
	 * @var int
	 */
	public $created;

	/**
	 * @var int
	 */
	public $timespent;
	
	/**
	 * @var Issue
	 */
	public $parent;

	/**
	 * @var array
	 */
	protected $_changelog;

	/** @var array */
	public $customFields = [];
	
	/**
	 * @param Project $project
	 * @param IssueType $issueType
	 *
	 * @return Issue
	 */
	public static function create(Project $project, IssueType $issueType) {
		$issue = new self([
			'issueType' => $issueType,
		]);
		$issue->_project = $project;

		return $issue;
	}

	/**
	 * @param Project $project
	 * @param array $data
	 * @return Issue[]
	 */
	public static function populateAll(Project $project, $data) {
		if (empty($data)) {
			return [];
		}
		$issues = [];
		foreach ($data as $issueData) {
			$issues[] = self::populate($project, $issueData);
		}

		return $issues;
	}

	/**
	 * @param Project $project
	 * @param array $data
	 * @param bool $loadCustomFields
	 * @return Issue
	 */
	public static function populate(Project $project, $data, $loadCustomFields = false) {
		if (!is_array($data) || !isset($data['id'])) {
			return null;
		}
		$issue = new self;
		$issue->_project = $project;
		$issue->id = (int)$data['id'];
		$issue->_key = $data['key'];

		$issue->_issueLinksRaw = $data['fields']['issuelinks'];

		$issue->summary = $data['fields']['summary'];
		$issue->status = Status::get($data['fields']['status']);
		$issue->priority = Priority::get($data['fields']['priority']);
		$issue->description = $data['fields']['description'];
		$issue->issueType = $project->getIssueType($data['fields']['issuetype']['name']);
		$issue->components = ArrayHelper::index($data['fields']['components'], 'name');
		$issue->timespent = $data['fields']['timespent'];
		$issue->created = strtotime($data['fields']['created']);
		$issue->customFields = [];
		
		if (isset($data['fields']['parent'])) {
			$issue->parent = self::populate($project, $data['fields']['parent'], $loadCustomFields);
		}

		if ($loadCustomFields)
		{
			foreach ($issue->issueType->getCustomFieldsMap() as $name => $id) {
				if (isset($data['fields']['customfield_' . $id])) {
					$issue->customFields[$name] = $data['fields']['customfield_' . $id];
				}
			}
		}

		return $issue;
	}

	/**
	 * @return Project
	 */
	public function getProject() {
		return $this->_project;
	}

	/**
	 * @return string
	 */
	public function getKey() {
		return $this->_key;
	}

	/**
	 * @return IssueLink[]
	 */
	public function getIssueLinks() {
		if ($this->_issueLinks === null)
		{
			$this->_issueLinks = [];

			foreach ($this->_issueLinksRaw as $issueLinkRaw) {
				$this->_issueLinks[] = IssueLink::populate($this->_project, $issueLinkRaw);
			}
		}

		return $this->_issueLinks;
	}
	
	/**
	 * @return string|null
	 * @throws \Exception
	 */
	public function getDuration() {
		if ($this->timespent === null) {
			return null;
		}

		$seconds = $this->timespent;

		$from = new \DateTime("@0");
		$to = new \DateTime("@$seconds");

		$diff = $from->diff($to);
		$values = [
			'd' => $diff->format('%a'),
			'h' => $diff->format('%h'),
			'm' => $diff->format('%i'),
			's' => $diff->format('%s'),
		];

		$duration = [];
		$values = array_filter($values);
		foreach ($values as $key => $value) {
			$duration[] = $value . $key;
		}

		return implode(' ', $duration);
	}
	
	/**
	 * @return string|null
	 */
	public function getLastChangelog() {
		$lastChangelog = null;

		if (isset($this->changelog['histories'])) {
			$history = end($this->changelog['histories']);
			$item = end($history['items']);

			$lastChangelog = $item['from'] . " -> " . $item['to'];
		}

		return $lastChangelog;
	}
	
	/**
	 * @return array|\SimpleXMLElement
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function getChangelog() {
		if ($this->_changelog === null)
		{
			$data = $this->project->client->get('issue/' . $this->key, ['fields' => 'changelog', 'expand' => 'changelog']);
			$this->_changelog = $data['changelog'];
		}

		return $this->_changelog;
	}
	
	/**
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function save() {
		if (!$this->key) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}
	
	/**
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	public function refresh() {
		if ($this->key) {
			$this->setAttributes($this->project->getIssue($this->key)->attributes, false);
		}

		return false;
	}
	
	/**
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	protected function insert() {
		$result = $this->project->client->post('issue', $this->serialize());
		if (!empty($result['errors'])) {
			$this->addErrors($result['errors']);
			return false;
		}
		else {
			$this->id = $result['id'];
			$this->_key = $result['key'];
		}
		$this->refresh();

		return true;
	}
	
	/**
	 * @return bool
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 * @throws \yii\base\InvalidConfigException
	 */
	protected function update() {
		$result = $this->project->client->put('issue/'.$this->key, $this->serialize());
		if (isset($result['errors'])) {
			$this->addErrors($result['errors']);

			return false;
		}
		$this->refresh();

		return true;
	}
	
	/**
	 * @return array
	 */
	public function serialize() {
		$fields = [
			'project' => [
				'id' => $this->project->id,
			],
			'issuetype' => $this->issueType,
		];
		if ($this->components && !empty($this->components)) {
			$fields['component'] = array_values($this->components);
		}
		if ($this->description) {
			$fields['description'] = $this->description;
		}
		if ($this->summary) {
			$fields['summary'] = $this->summary;
		}
		foreach ($this->issueType->getCustomFieldsMap() as $name => $id) {
			if(isset($this->customFields[$name])) {
				$fields['customfield_' . $id] = $this->customFields[$name];
			}
		}
		return [
			'fields' => $fields,
		];
	}
}
