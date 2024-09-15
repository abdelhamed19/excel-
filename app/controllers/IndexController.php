<?php

namespace app\controllers;

use PDO;
use helpers\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class indexController
{
    public function index()
    {
        return View::make('products/upload')->render();
    }
    public function upload()
    {
        $file = $_FILES['file']['tmp_name'];
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $pdo = new PDO('mysql:host=localhost;dbname=excel', 'root', '');

        // Prepare statements for category insertion and selection
        $categoryInsertStmt = $pdo->prepare("INSERT INTO categories (category, subcategory) VALUES (:category, :subcategory)");
        $categorySelectStmt = $pdo->prepare("SELECT id FROM categories WHERE category = :category AND subcategory = :subcategory");

        // Prepare statement for products insertion
        $productInsertStmt = $pdo->prepare("INSERT INTO products (category_id, code, name, description, size) 
                                            VALUES (:category_id, :code, :name, :description, :size)");

        $rowNumber = 0;

        foreach ($worksheet->getRowIterator() as $row) {
            $rowNumber++;
            if ($rowNumber === 1) {  // Skip header row
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = iterator_to_array($cellIterator);

            // Extract data from each row
            $category = $cells['A']->getValue();    // Column A
            $subcategory = $cells['B']->getValue(); // Column B
            $code = $cells['C']->getValue();        // Column C
            $name = $cells['D']->getValue();        // Column D
            $description = $cells['E']->getValue(); // Column E
            $size = $cells['F']->getValue();        // Column F

            // Skip if category or subcategory is empty
            if (empty($category) || empty($subcategory)) {
                continue;
            }

            // Check if category and subcategory already exist
            $categorySelectStmt->execute([':category' => $category, ':subcategory' => $subcategory]);
            $categoryRow = $categorySelectStmt->fetch(PDO::FETCH_ASSOC);

            if ($categoryRow) {
                // If category exists, get the category ID
                $categoryId = $categoryRow['id'];
            } else {
                // If category doesn't exist, insert it
                $categoryInsertStmt->execute([':category' => $category, ':subcategory' => $subcategory]);
                $categoryId = $pdo->lastInsertId(); // Get the last inserted ID
            }

            // Insert product data
            $productInsertStmt->execute([
                ':category_id' => $categoryId,
                ':code' => $code,
                ':name' => $name,
                ':description' => $description,
                ':size' => $size
            ]);
        }
        echo 'Data uploaded successfully';
    }
}
