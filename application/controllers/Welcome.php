<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load the database library
        $this->load->database();
    }

    public function index() {
        // Ambil tanggal saat ini dalam format dd-mm-yy
        $crdate = date("d-m-y");

        // String dinamis
        $string = "bisacoding-" . $crdate;

        // Buat hash MD5
        $md5_hash = md5($string);

        // Data yang akan dikirim dalam body POST request
        $username = "tesprogrammer150125C16";
        $password = $md5_hash; // Gunakan MD5 hash yang telah ditemukan

        // URL API
        $url = "https://recruitment.fastprint.co.id/tes/api_tes_programmer";

        // Inisialisasi cURL
        $ch = curl_init($url);

        // Set opsi cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // Menyusun data form-url-encoded untuk body
        $postData = [
            'username' => $username,
            'password' => $password
        ];

        // Mengirimkan data dengan format form-urlencoded menggunakan CURLOPT_POSTFIELDS
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        // Set header Content-Type menjadi application/x-www-form-urlencoded
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);

        // Eksekusi cURL dan ambil respons
        $response = curl_exec($ch);

        // Periksa jika ada error
        if(curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        } else {
            // Decode JSON response
            $responseData = json_decode($response, true);
            // var_dump($responseData);
            if ($responseData['error'] == 0) {
                // Disable foreign key checks temporarily
                $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

                // Clear all records in kategori, status, and produk tables
                $this->db->truncate('kategori');
                $this->db->truncate('status');
                $this->db->truncate('produk');

                // Reset auto-increment id to start from 1
                $this->db->query('ALTER TABLE kategori AUTO_INCREMENT = 1');
                $this->db->query('ALTER TABLE status AUTO_INCREMENT = 1');
                $this->db->query('ALTER TABLE produk AUTO_INCREMENT = 1');

                // Enable foreign key checks again
                $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

                // Loop through the products and insert into the database
                foreach ($responseData['data'] as $product) {
                    // Check if category exists in kategori table
                    $kategori = $this->db->get_where('kategori', ['nama_kategori' => $product['kategori']])->row_array();
                    if ($kategori) {
                        $kategori_id = $kategori['id_kategori']; // If exists, use the existing id
                    } else {
                        // Insert into kategori table if not exists
                        $kategoriData = ['nama_kategori' => $product['kategori']];
                        $this->db->insert('kategori', $kategoriData);
                        $kategori_id = $this->db->insert_id(); // Get the inserted id_kategori
                    }

                    // Check if status exists in status table
                    $status = $this->db->get_where('status', ['nama_status' => $product['status']])->row_array();
                    if ($status) {
                        $status_id = $status['id_status']; // If exists, use the existing id
                    } else {
                        // Insert into status table if not exists
                        $statusData = ['nama_status' => $product['status']];
                        $this->db->insert('status', $statusData);
                        $status_id = $this->db->insert_id(); // Get the inserted id_status
                    }

                    // Insert into produk table using the existing or newly inserted kategori_id and status_id
                    $produkData = [
                        'id_produk' => $product['id_produk'],
                        'nama_produk' => $product['nama_produk'],
                        'harga' => $product['harga'],
                        'kategori_id' => $kategori_id, // Use the found or newly inserted id_kategori
                        'status_id' => $status_id // Use the found or newly inserted id_status
                    ];
                    $this->db->insert('produk', $produkData);
                }

                echo "Data telah berhasil disimpan ke database.";
            }
        }

        // Tutup cURL
        curl_close($ch);
    }
}
