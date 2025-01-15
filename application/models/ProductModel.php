<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductModel extends CI_Model {


    // Get all products for DataTable
    public function get_products_ajax() {
        $this->db->select('p.id_produk, p.nama_produk, p.harga, k.nama_kategori, s.nama_status');
        $this->db->from('produk p');
        $this->db->join('kategori k', 'p.kategori_id = k.id_kategori');
        $this->db->join('status s', 'p.status_id = s.id_status');
        $this->db->where('s.id_status', 1);
        return $this->db->get()->result_array();
    }

    // Add product
    public function add_product($data) {
        $this->db->insert('produk', $data);
    }

    // Update product
    public function update_product($data) {
        $this->db->where('id_produk', $data['id_produk']);
        $this->db->update('produk', $data);
    }

    // Delete product
    public function delete_product($id) {
        $this->db->where('id_produk', $id);
        $this->db->delete('produk');
    }

    public function get_categories()
    {
        // Query to get categories from your database
        $this->db->select('id_kategori, nama_kategori');
        $this->db->from('kategori'); // Replace with your actual table name
        $query = $this->db->get();
        
        return $query->result_array();
    }

    // Fetch statuses from the database
    public function get_statuses()
    {
        // Query to get statuses from your database
        $this->db->select('id_status, nama_status');
        $this->db->from('status'); // Replace with your actual table name
        $query = $this->db->get();
        
        return $query->result_array();
    }

     // Method to get product details by ID
     public function get_product_by_id($id)
     {
         // Query to fetch product details by id_produk
         $this->db->select('id_produk, nama_produk, harga, kategori_id, status_id');
         $this->db->from('produk'); // Replace with your actual table name
         $this->db->where('id_produk', $id); // Filter by the given ID
         $query = $this->db->get();
 
         // Return the result if found, otherwise return null
         return $query->row_array();
     }

}
