<?php

class ModelExtensionModuleRestApi extends Model
{
	private $_table = 'rest_api_token';

	public function createTokenTable()
	{
		$test = $this->db->query("
			CREATE TABLE IF NOT EXISTS`" . DB_PREFIX . $this->_table . "` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(11) NOT NULL,
				`public_key` varchar(255) NOT NULL,
				`private_key` varchar(255) NOT NULL,
				`created_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function deleteTokenTable()
	{
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "rest_api_token`");
	}

	public function getTotalApis()
	{
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . $this->_table . "`");

		return $query->row['total'];
	}

	public function getApis($data = [])
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . $this->_table . "`";

		$sort_data = [
			'title',
			'public_key',
			'created_at'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY title";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getApi($api_id)
	{
		$sql = "SELECT * FROM `" . DB_PREFIX . $this->_table . "` WHERE `id` = ".$api_id;

		$query = $this->db->query($sql);

		return $query->rows[0];
	}

	public function addApi($post)
	{
		$result = $this->db->query("
			INSERT INTO " . DB_PREFIX . $this->_table . "
			SET `title` = '" . $post['title'] . "', `public_key` = '" . $post['public'] . "', `private_key` = '" . $post['private'] . "', `created_at` = NOW()
		");

		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function updateApi($post)
	{
		$result = $this->db->query("
			UPDATE " . DB_PREFIX . $this->_table . "
			SET `title` = '" . $post['title'] . "', `public_key` = '" . $post['public'] . "', `private_key` = '" . $post['private'] . "', `created_at` = NOW()
		WHERE id = ".$post['id'] );

		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function editApi($post)
	{
		$result = $this->db->query("
			INSERT INTO " . DB_PREFIX . $this->_table . "
			SET `title` = '" . $post['title'] . "', `public_key` = '" . $post['public'] . "', `private_key` = '" . $post['private'] . "', `created_at` = NOW()
		");

		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteApis( $ids )
	{
		$this->db->query( 'DELETE FROM '.DB_PREFIX.$this->_table.' WHERE `id` in ('.implode(',', $ids ).')' );

		return true;
	}
}

?>