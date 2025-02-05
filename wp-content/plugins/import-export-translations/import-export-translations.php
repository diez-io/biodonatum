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
use PhpOffice\PhpSpreadsheet\Style\Borders;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

function add_custom_post_row_action($actions, $post) {
    // Check if the post has taxonomy `taxonomy_language`
    if (has_term('', 'taxonomy_language', $post->ID)) {
        // Generate custom action link
        $export_url = admin_url('admin-post.php?action=export_acf_data&post_id=' . $post->ID);
        $actions['export_acf'] = '<a href="' . esc_url($export_url) . '">Export to excel file</a>';
    }

    return $actions;
}

add_filter('post_row_actions', 'add_custom_post_row_action', 10, 2);

// function export_acf_post_meta_to_csv() {
//     // Verify user capability
//     if (!current_user_can('edit_posts')) {
//         wp_die('Permission denied.');
//     }

//     // Validate the post_id parameter
//     if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
//         wp_die('Invalid post ID.');
//     }

//     $post_id = intval($_GET['post_id']);
//     $fields = get_fields($post_id);

//     if (empty($fields)) {
//         wp_die('No ACF fields found for post ID ' . $post_id);
//     }

//     // Set headers for CSV download
//     header('Content-Type: text/csv');
//     header('Content-Disposition: attachment; filename="acf_post_meta_' . $post_id . '.csv"');

//     $output = fopen('php://output', 'w');

//     // Write the header row
//     fputcsv($output, ['Field Name', 'Value']);

//     // Write each meta field row
//     foreach ($fields as $key => $value) {
//         fputcsv($output, [$key, is_array($value) ? json_encode($value) : $value]);
//     }

//     fclose($output);
//     exit;
// }

// function export_acf_post_meta_to_csv() {
//     if (!current_user_can('edit_posts')) {
//         wp_die('Permission denied.');
//     }

//     if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
//         wp_die('Invalid post ID.');
//     }

//     $post_id = intval($_GET['post_id']);
//     $fields = get_fields($post_id);

//     if (empty($fields)) {
//         wp_die('No ACF fields found for post ID ' . $post_id);
//     }

//     // Retrieve field groups for this post
//     $field_groups = acf_get_field_groups(['post_id' => $post_id]);

//     if (empty($field_groups)) {
//         wp_die('No ACF field groups found for post ID ' . $post_id);
//     }

//     // Map field keys to their corresponding tab names
//     $field_tabs = [];
//     foreach ($field_groups as $field_group) {
//         $fields_in_group = acf_get_fields($field_group['key']);
//         $current_tab = 'General'; // Default tab name if none is defined
//         if ($fields_in_group) {
//             foreach ($fields_in_group as $field) {
//                 if ($field['type'] === 'tab') {
//                     $current_tab = $field['label'];
//                 } else {
//                     $field_tabs[$field['name']] = $current_tab;
//                 }
//             }
//         }
//     }

//     // Group fields by their tab names
//     $grouped_fields = [];
//     foreach ($fields as $key => $value) {
//         $tab_name = isset($field_tabs[$key]) ? $field_tabs[$key] : 'Uncategorized';
//         $grouped_fields[$tab_name][] = [$key, is_array($value) ? json_encode($value) : $value];
//     }

//     // Set headers for CSV download
//     header('Content-Type: text/csv');
//     header('Content-Disposition: attachment; filename="acf_post_meta_' . $post_id . '.csv"');

//     $output = fopen('php://output', 'w');

//     // Write grouped fields to CSV
//     foreach ($grouped_fields as $tab_name => $fields) {
//         // Add a row for the tab name
//         fputcsv($output, [$tab_name, '', '']);

//         // Write fields under this tab
//         foreach ($fields as $field) {
//             fputcsv($output, ['', $field[0], $field[1]]);
//         }
//     }

//     fclose($output);
//     exit;
// }

function write_repeater_data($sheet, $row, $field_name, $post_id, $field_label) {
    $repeater_data = get_field($field_name, $post_id);

    if (empty($repeater_data)) {
        return $row;
    }

    // Write the repeater field label
    $sheet->setCellValue("A{$row}", $field_name);
    $sheet->setCellValue("B{$row}", "Repeater Field: $field_label");
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
        $columnIndex++;
        $label = $subfield['label'] ?? $subfield['name'];
        $sheet->setCellValue("{$columnIndex}{$row}", $label);
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
    foreach ($repeater_data as $subfield_data) {
        $columnIndex = 'A';

        foreach ($subfields as $subfield) {
            $columnIndex++;
            $subfield_name = $subfield['name'];
            $subfield_value = $subfield_data[$subfield_name] ?? '';

            // Handle arrays by converting them to JSON strings
            if (is_array($subfield_value)) {
                $subfield_value = json_encode($subfield_value);
            }

            $sheet->setCellValue("{$columnIndex}{$row}", $subfield_value);
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

function export_acf_to_excel() {
    if (!current_user_can('edit_posts')) {
        wp_die('Permission denied.');
    }

    if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
        wp_die('Invalid post ID.');
    }

    $post_id = intval($_GET['post_id']);
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
                    $row = write_repeater_data($sheet, $row, $field_name, $post_id, $field_label);
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

    // Set headers for the Excel download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="acf_post_meta.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


// Hook for logged-in users
add_action('admin_post_export_acf_data', 'export_acf_to_excel');
