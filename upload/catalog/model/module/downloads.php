<?php
class ModelModuleDownloads extends Model {
    public function getCategories() {
        $query = $this->db->query("
            SELECT dcd.*
            FROM " . DB_PREFIX . "down_categories dc
            LEFT JOIN " . DB_PREFIX . "down_categories_description dcd ON (dc.category_id = dcd.category_id AND dcd.language_id = '" . (int)$this->config->get('config_language_id') . "')
            WHERE dc.status = 1
            ORDER BY dc.sort_order, dcd.name
        ");

        return $query->rows;
    }

    public function getDownloads($category_ids = array()) {
        $sql = "
            SELECT d.*, dd.name, dd.description
            FROM " . DB_PREFIX . "down d
            LEFT JOIN " . DB_PREFIX . "down_descriptions dd ON (d.download_id = dd.download_id AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "')
            WHERE d.status = '1'
        ";

        if (!empty($category_ids)) {
            $sql .= "
                AND d.category_id IN (" . implode(',', $category_ids) . ")
            ";
        }

        $sql .= "
            ORDER BY d.sort_order, dd.name
        ";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getDownload($file_id) {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "down
            WHERE download_id = '" . (int)$file_id . "'
        ");

        return $query->row;
    }
}
?>