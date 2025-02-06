<?php
/*
Plugin Name: Import/Export Translations
Description: Import/Export Translations in excel format.
Version: 1.0
Author: DNT Group
*/

require_once plugin_dir_path( __FILE__ ) . '../../../vendor/autoload.php';


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\IOFactory;

// function add_custom_post_row_action($actions, $post) {
//     // Check if the post has taxonomy `taxonomy_language`
//     if (has_term('', 'taxonomy_language', $post->ID)) {
//         // Generate custom action link
//         $export_url = admin_url('admin-post.php?action=export_acf_data&post_id=' . $post->ID);
//         $actions['export_acf'] = '<a href="' . esc_url($export_url) . '">Export to excel file</a>';
//     }

//     return $actions;
// }

// add_filter('post_row_actions', 'add_custom_post_row_action', 10, 2);

// function write_repeater_data($sheet, $row, $field_name, $post_id, $field_label) {
//     $repeater_data = get_field($field_name, $post_id);

//     if (empty($repeater_data)) {
//         return $row;
//     }

//     // Write the repeater field label
//     $sheet->setCellValue("A{$row}", $field_name);
//     $sheet->setCellValue("B{$row}", "Repeater Field: $field_label:");
//     $row += 2;

//     // Fetch subfield structure from ACF
//     $field_object = get_field_object($field_name, $post_id);
//     $subfields = $field_object['sub_fields'] ?? [];

//     if (empty($subfields)) {
//         return $row;
//     }

//     // Write subtable headers with field labels
//     $columnIndex = 'A'; // Start from column B
//     foreach ($subfields as $subfield) {
//         if (in_array($subfield['type'], ['text', 'textarea'])) {
//             $columnIndex++;
//             $label = $subfield['label'] ?? $subfield['name'];
//             $sheet->setCellValue("{$columnIndex}{$row}", $label);
//         }
//     }

//     $sheet->getStyle("B{$row}:{$columnIndex}{$row}")->applyFromArray([
//         'fill' => [
//             'fillType' => Fill::FILL_SOLID,
//             'startColor' => ['argb' => 'D3D3D3'], // Light grey background color
//         ],
//         'alignment' => [
//             'horizontal' => Alignment::HORIZONTAL_CENTER,
//             'vertical' => Alignment::VERTICAL_CENTER,
//         ],
//     ]);

//     $firstRow = $row;

//     $row++;

//     // Write the subfield data for each repeater row
//     foreach ($repeater_data as $i => $subfield_data) {
//         $columnIndex = 'A';

//         foreach ($subfields as $subfield) {
//             if (in_array($subfield['type'], ['text', 'textarea'])) {
//                 $columnIndex++;
//                 $subfield_name = $subfield['name'];
//                 $subfield_value = get_field($field_name . "_{$i}_" . $subfield_name, $post_id, false);

//                 // Handle arrays by converting them to JSON strings
//                 if (is_array($subfield_value)) {
//                     $subfield_value = json_encode($subfield_value);
//                 }

//                 $sheet->setCellValue("{$columnIndex}{$row}", $subfield_value);
//             }
//         }

//         $row++;
//     }

//     $lastRow = $row - 1; // The last row in the repeater subtable

//     $sheet->getStyle("B{$firstRow}:{$columnIndex}{$lastRow}")->applyFromArray([
//         'borders' => [
//             'outline' => [
//                 'borderStyle' => Border::BORDER_THICK,
//                 'color' => ['argb' => '000000'], // Black border
//             ],
//             'allBorders' => [
//                 'borderStyle' => Border::BORDER_THIN,
//                 'color' => ['argb' => '000000'], // Thin black borders between cells
//             ],
//         ],
//     ]);


//     // Add a blank row for better spacing
//     $row++;
//     return $row;
// }

// function export_acf_to_excel() {
//     if (!current_user_can('edit_posts')) {
//         wp_die('Permission denied.');
//     }

//     if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
//         wp_die('Invalid post ID.');
//     }

//     $post_id = intval($_GET['post_id']);
//     $post_title = strtolower(str_replace(' ', '_', get_the_title($post_id)));
//     $fields = get_fields($post_id);

//     if (empty($fields)) {
//         wp_die('No ACF fields found for this post.');
//     }

//     $spreadsheet = new Spreadsheet();

//     // Fetch ACF field groups for the post type
//     $field_groups = acf_get_field_groups(['post_id' => $post_id]);

//     if (empty($field_groups)) {
//         wp_die('No ACF field groups found for this post.');
//     }

//     $sheetIndex = 0;

//     // Iterate through each field group to process tabs
//     foreach ($field_groups as $field_group) {
//         $fields_in_group = acf_get_fields($field_group);

//         if (!$fields_in_group) continue;

//         $tabFields = [];
//         $currentTab = 'Default Tab';

//         // Organize fields under tabs
//         foreach ($fields_in_group as $field) {
//             if ($field['type'] === 'tab') {
//                 $currentTab = $field['label'];
//                 if (!isset($tabFields[$currentTab])) {
//                     $tabFields[$currentTab] = [];
//                 }
//             } else {
//                 $tabFields[$currentTab][] = $field;
//             }
//         }

//         // Create sheets for each tab
//         foreach ($tabFields as $tabName => $fields) {
//             if ($sheetIndex === 0) {
//                 $sheet = $spreadsheet->getActiveSheet();
//                 $sheet->setTitle(substr($tabName, 0, 31));
//             } else {
//                 $sheet = $spreadsheet->createSheet();
//                 $sheet->setTitle(substr($tabName, 0, 31));
//             }

//             // Set headers for the tab sheet
//             $sheet->setCellValue('A1', 'slug');
//             $sheet->setCellValue('B1', 'Field Name');
//             $sheet->setCellValue('C1', 'Default Value');
//             $sheet->setCellValue('D1', 'Value');

//             $sheet->getStyle("A1:D1")->applyFromArray([
//                 'fill' => [
//                     'fillType' => Fill::FILL_SOLID,
//                     'startColor' => ['argb' => 'D3D3D3'], // Light grey background color
//                 ],
//                 'alignment' => [
//                     'horizontal' => Alignment::HORIZONTAL_CENTER,
//                     'vertical' => Alignment::VERTICAL_CENTER,
//                 ],
//                 'borders' => [
//                     'top' => [
//                         'borderStyle' => Border::BORDER_THICK,
//                         'color' => ['argb' => '000000'], // Black border
//                     ],
//                     'left' => [
//                         'borderStyle' => Border::BORDER_THICK,
//                         'color' => ['argb' => '000000'], // Black border
//                     ],
//                     'right' => [
//                         'borderStyle' => Border::BORDER_THICK,
//                         'color' => ['argb' => '000000'], // Black border
//                     ],
//                     'allBorders' => [
//                         'borderStyle' => Border::BORDER_THIN,
//                         'color' => ['argb' => '000000'], // Thin black borders between cells
//                     ],
//                 ],
//             ]);

//             $row = 2;

//             foreach ($fields as $field) {
//                 if (!in_array($field['type'], ['text', 'textarea', 'repeater'])) {
//                     continue;
//                 }

//                 $field_name = $field['name'];
//                 $field_label = $field['label'] ?? $field_name;
//                 $default_value = $field['default_value'] ?? '';
//                 $field_value = get_post_meta($post_id, $field_name, true); // Raw unformatted value

//                 if ($field['type'] === 'repeater') {
//                     $row = write_repeater_data($sheet, $row, $field_name, $post_id, $field_label);
//                 }
//                 else {
//                     $sheet->setCellValue("A{$row}", $field_name);
//                     $sheet->setCellValue("B{$row}", $field_label);
//                     $sheet->setCellValue("C{$row}", $default_value);
//                     $sheet->setCellValue("D{$row}", $field_value);
//                     $row++;
//                 }
//             }

//             $sheetIndex++;
//         }
//     }

//     foreach ($spreadsheet->getAllSheets() as $sheet) {
//         $sheet->getColumnDimension('A')->setAutoSize(true);
//         $sheet->getColumnDimension('B')->setWidth(30);
//         $sheet->getColumnDimension('C')->setWidth(70);
//         $sheet->getColumnDimension('D')->setWidth(70);

//         $sheet->getStyle('B')->getAlignment()->setWrapText(true);
//         $sheet->getStyle('C')->getAlignment()->setWrapText(true);
//         $sheet->getStyle('D')->getAlignment()->setWrapText(true);

//         $sheet->getStyle('A')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//         $sheet->getStyle('B')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
//         $sheet->getStyle('C')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
//         $sheet->getStyle('D')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
//     }

//     // Set headers for the Excel download
//     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//     header('Content-Disposition: attachment;filename="' . $post_title . '.xlsx"');
//     header('Cache-Control: max-age=0');

//     $writer = new Xlsx($spreadsheet);
//     $writer->save('php://output');
//     exit;
// }


// Hook for logged-in users
//add_action('admin_post_export_acf_data', 'export_acf_to_excel');




/////////////////////
// Import
///////////////


// add_filter('post_row_actions', 'add_acf_import_action', 10, 2);

// function add_acf_import_action($actions, $post) {
//     if (!current_user_can('edit_post', $post->ID)) {
//         return $actions;
//     }

//     // Create nonce for security
//     $nonce = wp_create_nonce('acf_import_nonce');

//     // Build the action link
//     $import_url = add_query_arg([
//         'post_id' => $post->ID,
//         '_acf_nonce' => $nonce,
//         'acf_import_action' => 'show_import_form'
//     ], admin_url('edit.php'));

//     $actions['acf_import'] = '<a href="' . esc_url($import_url) . '">Import ACF Data</a>';

//     return $actions;
// }

// add_action('admin_init', 'handle_acf_import_action');

// function handle_acf_import_action() {
//     if (!isset($_GET['acf_import_action']) || $_GET['acf_import_action'] !== 'show_import_form') {
//         return;
//     }

//     // Verify nonce
//     if (!isset($_GET['_acf_nonce']) || !wp_verify_nonce($_GET['_acf_nonce'], 'acf_import_nonce')) {
//         wp_die('Invalid nonce specified.');
//     }

//     $post_id = intval($_GET['post_id']);
//     if (!$post_id || !current_user_can('edit_post', $post_id)) {
//         wp_die('Permission denied.');
//     }

//     // Display the import form
//     echo '<div class="wrap"><h1>Import ACF Data</h1>';
//     echo '<form method="post" enctype="multipart/form-data">';
//     echo '<input type="file" name="acf_excel_file" accept=".xlsx, .xls" />';
//     echo '<input type="hidden" name="post_id" value="' . esc_attr($post_id) . '" />';
//     wp_nonce_field('acf_excel_import', 'acf_excel_nonce'); // CSRF nonce for form
//     submit_button('Import ACF Data');
//     echo '</form></div>';

//     // Handle form submission
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acf_excel_nonce'])) {
//         if (!wp_verify_nonce($_POST['acf_excel_nonce'], 'acf_excel_import')) {
//             wp_die('Invalid form nonce.');
//         }

//         if (!empty($_FILES['acf_excel_file']['tmp_name'])) {
//             handle_acf_excel_import($_FILES['acf_excel_file']['tmp_name'], $post_id);
//         }
//     }

//     exit;
// }

// function handle_acf_excel_import($file_path, $post_id) {
//     // Implement file parsing and ACF updating logic here
//     echo '<div class="notice notice-success"><p>File imported successfully!</p></div>';
// }


// function import_acf_from_excel($file_path, $post_id) {
//     // Load the spreadsheet
//     $spreadsheet = IOFactory::load($file_path);
//     $sheetCount = $spreadsheet->getSheetCount();

//     // Iterate through all sheets (corresponding to ACF field groups/tabs)
//     for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
//         $sheet = $spreadsheet->getSheet($sheetIndex);
//         $sheetTitle = $sheet->getTitle();

//         // Skip the sheet if no data in it
//         if ($sheet->getHighestRow() <= 1) continue;

//         // Iterate through each row (skip the header)
//         $highestRow = $sheet->getHighestRow();
//         for ($row = 2; $row <= $highestRow; $row++) {
//             $field_name = $sheet->getCell('A' . $row)->getValue();
//             $field_label = $sheet->getCell('B' . $row)->getValue();
//             $default_value = $sheet->getCell('C' . $row)->getValue();
//             $field_value = $sheet->getCell('D' . $row)->getValue();

//             // Handle different field types (normal fields, repeater fields, etc.)
//             if ($field_value !== null) {
//                 // Check if this is a repeater field
//                 if ($field_name && strpos($field_name, 'repeater') !== false) {
//                     $repeater_data = [];

//                     // Get the subfield values for this repeater
//                     $col = 'B';
//                     while ($sheet->getCell($col . $row)->getValue()) {
//                         $subfield_value = $sheet->getCell($col . $row)->getValue();
//                         $subfield_name = $sheet->getCell($col . '1')->getValue();
//                         $repeater_data[][$subfield_name] = $subfield_value;
//                         $col++;
//                     }

//                     // Update repeater field in ACF
//                     update_field($field_name, $repeater_data, $post_id);
//                 } else {
//                     // Update normal fields
//                     update_field($field_name, $field_value, $post_id);
//                 }
//             }
//         }
//     }
// }

class ImportExportTranslations {
    private static $instance;

    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_filter('post_row_actions', [$this, 'add_import_export_actions'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_ajax_acf_import_excel', [$this, 'handle_acf_import_ajax']);
        add_action('admin_post_export_acf_data', [$this, 'export_acf_to_excel']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('import-translation-handler', plugin_dir_url(__FILE__) . 'assets/js/main.js', ['jquery'], null, true);

        wp_localize_script('import-translation-handler', 'acfExcelHandler', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'importNonce' => wp_create_nonce('acf_excel_import_nonce'),
        ]);
    }

    public function add_import_export_actions($actions, $post) {
        if (!current_user_can('edit_post', $post->ID)) {
            return $actions;
        }

        if (has_term('', 'taxonomy_language', $post->ID)) {
            $export_url = add_query_arg([
                'action' => 'export_acf_data',
                'post_id' => $post->ID,
            ], admin_url('admin-post.php'));

            $actions['acf_import'] = '<a href="#" class="acf-import-link" data-post-id="' . esc_attr($post->ID) . '">Import from excel file</a>';
            $actions['acf_export'] = '<a href="' . esc_url($export_url) . '">Export to excel file</a>';
        }

        return $actions;
    }

    public function handle_acf_import_ajax() {
        check_ajax_referer('acf_excel_import_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Permission denied.');
        }

        if (!isset($_FILES['acf_excel_file']['tmp_name']) || empty($_FILES['acf_excel_file']['tmp_name'])) {
            wp_send_json_error('No file uploaded.');
        }

        $file_path = $_FILES['acf_excel_file']['tmp_name'];
        $this->handle_acf_excel_import($file_path, $post_id);

        wp_send_json_success('File imported successfully!');
    }

    private function handle_acf_excel_import($file_path, $post_id) {
        // Load the spreadsheet
        $spreadsheet = IOFactory::load($file_path);
        $sheetCount = $spreadsheet->getSheetCount();

        // Iterate through all sheets (corresponding to ACF field groups/tabs)
        for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
            $sheet = $spreadsheet->getSheet($sheetIndex);
            $sheetTitle = $sheet->getTitle();

            // Skip the sheet if no data in it
            if ($sheet->getHighestRow() <= 1) continue;

            // Iterate through each row (skip the header)
            $highestRow = $sheet->getHighestRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $field_name = $sheet->getCell('A' . $row)->getValue();
                $field_value = $sheet->getCell('D' . $row)->getValue();

                error_log('field name: ' . $field_name . '; value: ' . $field_value);

                $field_object = get_field_object($field_name, $post_id);

                if ($field_object) {
                    $field_type = $field_object['type'];

                    error_log('field_type: ' . $field_type);

                    if ($field_type === 'repeater') {
                        $repeater_data = [];
                        $subfields = $field_object['sub_fields'] ?? [];

                        $row += 3; // Skip the empty row and header row

                        // Parse each row until an empty row is encountered
                        while (true) {
                            $col = 'B'; // Start reading values from column B
                            $row_data = [];

                            foreach ($subfields as $subfield) {
                                $subfield_name = $subfield['name'];
                                $subfield_value = $sheet->getCell($col . $row)->getValue();

                                if (empty($subfield_value)) {
                                    break; // Stop processing this row if an empty cell is encountered
                                }

                                $row_data[$subfield_name] = $subfield_value;
                                $col++;
                            }

                            if (empty($row_data)) {
                                break; // Stop parsing when no valid data is found in the row
                            }

                            $repeater_data[] = $row_data;
                            $row++;
                        }

                        if (!empty($repeater_data)) {
                            update_field($field_name, $repeater_data, $post_id);
                            error_log('Updated repeater field: ' . $field_name);
                        }
                    }
                    elseif ($field_value !== null) {
                        // Update normal fields
                        error_log('Update normal fields');

                        update_field($field_name, $field_value, $post_id);
                    }
                }
                else {
                    error_log("Field object not found for: $field_name");
                }
            }
        }
    }

    public function write_repeater_data($sheet, $row, $field_name, $post_id, $field_label) {
        $repeater_data = get_field($field_name, $post_id);

        if (empty($repeater_data)) {
            return $row;
        }

        // Write the repeater field label
        $sheet->setCellValue("A{$row}", $field_name);
        $sheet->setCellValue("B{$row}", "Repeater Field: $field_label:");
        $row += 2;

        // Fetch subfield structure from ACF
        $field_object = get_field_object($field_name, $post_id);
        $subfields = $field_object['sub_fields'] ?? [];

        if (empty($subfields)) {
            return $row;
        }

        // Write subtable headers with field labels
        $columnIndex = 'A'; // Start from column B
        foreach ($subfields as $subfield) {
            if (in_array($subfield['type'], ['text', 'textarea'])) {
                $columnIndex++;
                $label = $subfield['label'] ?? $subfield['name'];
                $sheet->setCellValue("{$columnIndex}{$row}", $label);
            }
        }

        $sheet->getStyle("B{$row}:{$columnIndex}{$row}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'D3D3D3'], // Light grey background color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $firstRow = $row;

        $row++;

        // Write the subfield data for each repeater row
        foreach ($repeater_data as $i => $subfield_data) {
            $columnIndex = 'A';

            foreach ($subfields as $subfield) {
                if (in_array($subfield['type'], ['text', 'textarea'])) {
                    $columnIndex++;
                    $subfield_name = $subfield['name'];
                    $subfield_value = get_field($field_name . "_{$i}_" . $subfield_name, $post_id, false);

                    // Handle arrays by converting them to JSON strings
                    if (is_array($subfield_value)) {
                        $subfield_value = json_encode($subfield_value);
                    }

                    $sheet->setCellValue("{$columnIndex}{$row}", $subfield_value);
                }
            }

            $row++;
        }

        $lastRow = $row - 1; // The last row in the repeater subtable

        $sheet->getStyle("B{$firstRow}:{$columnIndex}{$lastRow}")->applyFromArray([
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK,
                    'color' => ['argb' => '000000'], // Black border
                ],
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'], // Thin black borders between cells
                ],
            ],
        ]);


        // Add a blank row for better spacing
        $row++;
        return $row;
    }

    public function export_acf_to_excel() {
        if (!current_user_can('edit_posts')) {
            wp_die('Permission denied.');
        }

        if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
            wp_die('Invalid post ID.');
        }

        $post_id = intval($_GET['post_id']);
        $post_title = strtolower(str_replace(' ', '_', get_the_title($post_id)));
        $fields = get_fields($post_id);

        if (empty($fields)) {
            wp_die('No ACF fields found for this post.');
        }

        $spreadsheet = new Spreadsheet();

        // Fetch ACF field groups for the post type
        $field_groups = acf_get_field_groups(['post_id' => $post_id]);

        if (empty($field_groups)) {
            wp_die('No ACF field groups found for this post.');
        }

        $sheetIndex = 0;

        // Iterate through each field group to process tabs
        foreach ($field_groups as $field_group) {
            $fields_in_group = acf_get_fields($field_group);

            if (!$fields_in_group) continue;

            $tabFields = [];
            $currentTab = 'Default Tab';

            // Organize fields under tabs
            foreach ($fields_in_group as $field) {
                if ($field['type'] === 'tab') {
                    $currentTab = $field['label'];
                    if (!isset($tabFields[$currentTab])) {
                        $tabFields[$currentTab] = [];
                    }
                } else {
                    $tabFields[$currentTab][] = $field;
                }
            }

            // Create sheets for each tab
            foreach ($tabFields as $tabName => $fields) {
                if ($sheetIndex === 0) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle(substr($tabName, 0, 31));
                } else {
                    $sheet = $spreadsheet->createSheet();
                    $sheet->setTitle(substr($tabName, 0, 31));
                }

                // Set headers for the tab sheet
                $sheet->setCellValue('A1', 'slug');
                $sheet->setCellValue('B1', 'Field Name');
                $sheet->setCellValue('C1', 'Default Value');
                $sheet->setCellValue('D1', 'Value');

                $sheet->getStyle("A1:D1")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'D3D3D3'], // Light grey background color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'], // Black border
                        ],
                        'left' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'], // Black border
                        ],
                        'right' => [
                            'borderStyle' => Border::BORDER_THICK,
                            'color' => ['argb' => '000000'], // Black border
                        ],
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Thin black borders between cells
                        ],
                    ],
                ]);

                $row = 2;

                foreach ($fields as $field) {
                    if (!in_array($field['type'], ['text', 'textarea', 'repeater'])) {
                        continue;
                    }

                    $field_name = $field['name'];
                    $field_label = $field['label'] ?? $field_name;
                    $default_value = $field['default_value'] ?? '';
                    $field_value = get_post_meta($post_id, $field_name, true); // Raw unformatted value

                    if ($field['type'] === 'repeater') {
                        $row = $this->write_repeater_data($sheet, $row, $field_name, $post_id, $field_label);
                    }
                    else {
                        $sheet->setCellValue("A{$row}", $field_name);
                        $sheet->setCellValue("B{$row}", $field_label);
                        $sheet->setCellValue("C{$row}", $default_value);
                        $sheet->setCellValue("D{$row}", $field_value);
                        $row++;
                    }
                }

                $sheetIndex++;
            }
        }

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(70);
            $sheet->getColumnDimension('D')->setWidth(70);

            $sheet->getStyle('B')->getAlignment()->setWrapText(true);
            $sheet->getStyle('C')->getAlignment()->setWrapText(true);
            $sheet->getStyle('D')->getAlignment()->setWrapText(true);

            $sheet->getStyle('A')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('B')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('C')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            $sheet->getStyle('D')->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        }

        // Set headers for the Excel download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $post_title . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}

ImportExportTranslations::get_instance();
