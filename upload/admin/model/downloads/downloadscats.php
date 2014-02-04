<?php
class ModelDownloadsDownloadscats extends Model {
    public function addCategory($data) {
        $this->db->query("
            INSERT INTO " . DB_PREFIX . "down_categories
            SET
                sort_order = '" . (int)$data['sort_order'] . "',
                status = '" . (int)$data['status'] . "'
        ");

        $category_id = $this->db->getLastId();

        foreach ($data['description'] as $language_id => $value) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "down_categories_description
                SET
                    category_id = '" . (int)$category_id . "',
                    language_id = '" . (int)$language_id . "',
                    name = '" . $this->db->escape($value['name']) . "',
                    description = '" . $this->db->escape($value['description']) . "'
                ");
        }

        $this->cache->delete('category');
    }

    public function editCategory($category_id, $data) {
        $this->db->query("
            UPDATE " . DB_PREFIX . "down_categories
            SET
                sort_order = '" . (int)$data['sort_order'] . "',
                status = '" . (int)$data['status'] . "'
            WHERE category_id = '" . (int)$category_id . "'
        ");

        $this->db->query("
            DELETE
            FROM " . DB_PREFIX . "down_categories_description
            WHERE category_id = '" . (int)$category_id . "'
        ");

        foreach ($data['description'] as $language_id => $value) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "down_categories_description
                SET
                    category_id = '" . (int)$category_id . "',
                    language_id = '" . (int)$language_id . "',
                    name = '" . $this->db->escape($value['name']) . "',
                    description = '" . $this->db->escape($value['description']) . "'
            ");
        }

        $this->cache->delete('category');
    }

    public function deleteCategory($category_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "down_categories WHERE category_id = '" . (int)$category_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "down_categories_description WHERE category_id = '" . (int)$category_id . "'");

        $this->cache->delete('category');
    }

    public function getCategory($category_id) {
        $query = $this->db->query("
            SELECT c.*
            FROM " . DB_PREFIX . "down_categories c
            LEFT JOIN " . DB_PREFIX . "down_categories_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND c.category_id = '" . (int)$category_id . "'
            GROUP BY c.category_id
        ");

        return $query->row;
    }

    public function getCategories($data) {
        $sql = "
            SELECT c.category_id, cd.name, c.sort_order, c.status
            FROM " . DB_PREFIX . "down_categories c
            LEFT JOIN " . DB_PREFIX . "down_categories_description cd ON (c.category_id = cd.category_id)
            WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY c.category_id
            ORDER BY name
        ";

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getCategoryDescriptions($category_id) {
        $down_categories_description_data = array();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "down_categories_description
            WHERE category_id = '" . (int)$category_id . "'
        ");

        foreach ($query->rows as $result) {
            $down_categories_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'      => $result['description']
            );
        }

        return $down_categories_description_data;
    }

    public function getTotalCategories() {
        $query = $this->db->query("
            SELECT COUNT(*) AS total
            FROM " . DB_PREFIX . "down_categories
        ");

        return $query->row['total'];
    }
}
?>