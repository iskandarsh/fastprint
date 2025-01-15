<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('ProductModel');
    }

    // Fetch products with "Bisa Dijual" status
    public function index() {
       
        $this->load->view('products_view');
    }

    // Fetch all products for DataTable
    public function get_products_ajax() {
        $data = $this->ProductModel->get_products_ajax();
        echo json_encode($data);
    }

    // Add product
    public function add_product() {
        $data = $this->input->post();
        $this->ProductModel->add_product($data);
        echo json_encode(['status' => 'success']);
    }

    // Update product
    public function update_product() {
        $data = $this->input->post();
        $this->ProductModel->update_product($data);
        echo json_encode(['status' => 'success']);
    }

    // Delete product
    public function delete_product($id) {
        $this->ProductModel->delete_product($id);
        echo json_encode(['status' => 'success']);
    }

     // Method to get categories for Select2 dropdown
     public function get_categories()
     {
         // Fetch categories from the database
         $categories = $this->ProductModel->get_categories();
 
         // Prepare the response data
         echo json_encode($categories);
     }
 
     // Method to get statuses for Select2 dropdown
     public function get_statuses()
     {
         // Fetch statuses from the database
         $statuses = $this->ProductModel->get_statuses();
 
         // Prepare the response data
         echo json_encode($statuses);
     }

       // Method to get a product by ID
    public function get_product_by_id($id)
    {
        // Fetch product details from the database using the provided ID
        $product = $this->ProductModel->get_product_by_id($id);

        // Check if product is found
        if ($product) {
            // Return product details as a JSON response
            echo json_encode($product);
        } else {
            // Return an error if the product is not found
            echo json_encode(['error' => 'Product not found']);
        }
    }

}
