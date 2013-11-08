<?php
class Location extends AppModel {
	public $name = 'Location';
	public $useTable = false;
	public $simplified_name;
	public $full_name;
	public $location_type;
	public $id;

	public function getCountyIDFromFips($fips) {
		if ($cache = Configure::read('cache_location_queries')) {
			$cache_key = "getCountyIDFromFips($fips)";
			if ($cached = Cache::read($cache_key)) {
				return $cached;
			}
		}
		$this->setSource('counties');
		$result = $this->find('first', array(
			'conditions' => array('fips' => $fips),
			'contain' => false,
			'fields' => array('id')
		));
		$result = $result ? $result['Location']['id'] : false;
		if ($cache) {
			Cache::write($cache_key, $result);
		}
		return $result;
	}

	public function getCountiesFull() {
		if ($cache = Configure::read('cache_location_queries')) {
			$cache_key = "getCountiesFull()";
			if ($cached = Cache::read($cache_key)) {
				return $cached;
			}
		}
		$this->setSource('counties');
		$this->displayField = 'name';
		$result = $this->find('all', array(
			'fields' => array('id', 'name', 'state'),
			'order' => array('name')
		));
		if ($result) {
			$retval = array();
			foreach ($result as $row) {
				$retval[] = $row['Location'];
			}
		} else {
			$retval = false;
		}
		if ($cache) {
			Cache::write($cache_key, $retval);
		}
		return $retval;
	}

	public function getCountiesSortedByState() {
		$counties = $this->getCountiesFull();
		$counties_sorted = array();
		foreach ($counties as $county) {
			$counties_sorted[$county['state']][$county['id']] = $county['name'];
		}
		return $counties_sorted;
	}

	public function getCountyNameFromID($county_id) {
		if ($cache = Configure::read('cache_location_queries')) {
			$cache_key = "getCountyNameFromID($county_id)";
			if ($cached = Cache::read($cache_key)) {
				return $cached;
			}
		}
		$this->setSource('counties');
		$result = $this->find('first', array(
			'conditions' => array('id' => $county_id),
			'contain' => false,
			'fields' => array('name')
		));
		$result = $result ? $result['Location']['name'] : false;
		if ($cache) {
			Cache::write($cache_key, $result);
		}
		return $result;
	}
}