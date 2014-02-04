<?php
class ModelDownloadsDownloadsitems extends Model {
	public function addDownloadsitem($data) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "down
			SET
			    category_id = '" . (int)$data['category_id'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				status = '" . (int)$data['status'] . "',
                mask = '" . $this->db->escape($data['mask']) . "',
				filename = '" . $this->db->escape($data['filename']) . "',
				image = '" . $data['image'] . "',
				filesize = '" . (int)$data['filesize'] . "'
		");

		$downloaditem_id = $this->db->getLastId();

		foreach ($data['description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "down_descriptions
				SET
				    download_id = '" . (int)$downloaditem_id . "',
					language_id = '" . (int)$language_id . "',
					name = '" . $this->db->escape($value['name']) . "',
					description = '" . $this->db->escape($value['description']) . "'
			");
		}

		return $downloaditem_id;
	}

	public function editDownloadsitem($downloaditem_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "down
			SET
			    category_id = '" . (int)$data['category_id'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				status = '" . (int)$data['status'] . "',
                mask = '" . $this->db->escape($data['mask']) . "',
				filename = '" . $this->db->escape($data['filename']) . "',
				image = '" . $data['image'] . "',
				filesize = '" . (int)$data['filesize'] . "'
				WHERE download_id = '" . (int)$downloaditem_id . "'
		");

		$this->db->query("DELETE FROM " . DB_PREFIX . "down_descriptions WHERE download_id = '" . (int)$downloaditem_id . "'");

		foreach ($data['description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "down_descriptions
				SET
				    download_id = '" . (int)$downloaditem_id . "',
					language_id = '" . (int)$language_id . "',
					name = '" . $this->db->escape($value['name']) . "',
					description = '" . $this->db->escape($value['description']) . "'
			");
		}
	}

	public function deleteDownloadsitem($downloaditem_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "down WHERE download_id = '" . (int)$downloaditem_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "down_descriptions WHERE download_id = '" . (int)$downloaditem_id . "'");
	}

	public function getDownloadsitem($downloaditem_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "down WHERE download_id = '" . (int)$downloaditem_id . "'");

		return $query->row;
	}

	public function getDownloadsitems() {
		$download_data = array();

		$query = $this->db->query("
			SELECT c.*, cd.*
			FROM " . DB_PREFIX . "down c
			LEFT JOIN " . DB_PREFIX . "down_descriptions cd ON (c.download_id = cd.download_id)
			WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			ORDER BY c.sort_order, cd.name ASC
		");

		foreach ($query->rows as $result) {
			$download_data[] = array(
				'download_id' => $result['download_id'],
				'name'        => $result['name'],
				'status'  	  => $result['status'],
				'sort_order'  => $result['sort_order']
			);
		}

		return $download_data;
	}

	public function getDownloadsDescriptions($download_id) {
		$menu_description_data = array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "down_descriptions
			WHERE download_id = '" . (int)$download_id . "'
		");

		foreach ($query->rows as $result) {
			$menu_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description']
			);
		}

		return $menu_description_data;
	}

    public function getCategories() {
        $query = $this->db->query("
            SELECT c.category_id, cd.name
            FROM " . DB_PREFIX . "down_categories c
            LEFT JOIN " . DB_PREFIX . "down_categories_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY c.category_id
            ORDER BY name
        ");

        return $query->rows;
    }

    public function getDownloadsitemsFiles($ids) {
        $query = $this->db->query("
            SELECT download_id, filename
            FROM " . DB_PREFIX . "down
            WHERE download_id IN (" . $ids . ")
        ");

        return $query->rows;
    }

    public function getDownloadsitemsFile($id) {
        $query = $this->db->query("
            SELECT filename
            FROM " . DB_PREFIX . "down
            WHERE download_id = " . $id . "
        ");

        return $query->row['filename'];
    }
}
?>