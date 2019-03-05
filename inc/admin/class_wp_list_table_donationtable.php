<?php
// Integrate WP List Table for Donation Table

if (!class_exists('WP_List_Table')) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FP_List_Table_DonationTable extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        $data = $this->table_data();

        if (isset($_REQUEST['s'])) {
            $searchvalue = $_REQUEST['s'];
            $keyword = "/$searchvalue/";

            $newdata = array();
            foreach ($data as $eacharray => $value) {
                $searchfunction = preg_grep($keyword, $value);
                if (!empty($searchfunction)) {
                    $newdata[] = $data[$eacharray];
                }
            }
            usort($newdata, array(&$this, 'sort_data'));

            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($newdata);

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            $newdata = array_slice($newdata, (($currentPage - 1) * $perPage), $perPage);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $newdata;
        } else {
            usort($data, array(&$this, 'sort_data'));

            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($data);

            $this->set_pagination_args(array(
                'total_items' => $totalItems,
                'per_page' => $perPage
            ));

            $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

            $this->_column_headers = array($columns, $hidden, $sortable);

            $this->items = $data;
        }
    }

    public function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
            'sno' => __('S.No', 'donationsystem'),
            'donarname' => __('Donar Name', 'donationsystem'),
            'email' => __('Donar Email', 'donationsystem'),
            'amount' => __('Donated Amount', 'donationsystem'),
            'orderid' => __('Order Id', 'donationsystem'),
            'memorable' => __('Memorable Name', 'donationsystem'),
            'honorable' => __('Honorable Name', 'donationsystem'),
            'reason' => __('Reason For Donation', 'donationsystem'),
            'status' => __('Payment Status', 'donationsystem'),
            'url' => __('URL', 'donationsystem'),
            'date' => __('Date', 'donationsystem'),
        );

        return $columns;
    }

    public function get_hidden_columns() {
        return array();
    }

    public function get_sortable_columns() {
        return array(
            'amount' => array('amount', false),
            'sno' => array('sno', false),
            'date' => array('date', false),
        );
    }

    private function table_data() {
        $data = array();
        $i = 1;

        $get_list_orderids = FP_Donation_Product_Function::get_donated_order_ids();
        ?><style type="text/css">
            .reason_for{
                width: auto; 
                overflow: hidden; 
                white-space: nowrap;
                text-overflow: ellipsis; 
            }
            div.reason_for:hover{
                width: auto;
                overflow: auto;
                text-overflow: clip;
                white-space: normal;
            }
        </style>
        <?php
        if (is_array($get_list_orderids) && (!empty($get_list_orderids))) {
            foreach ($get_list_orderids as $value) {
                $donatedamount = get_post_meta($value, 'fp_donation_value', true);
                $reason_type = get_post_meta($value, 'fp_donation_reason_type', true);
                $donate_for_person = get_post_meta($value, 'fp_donation_person', true) != "" ? get_post_meta($value, 'fp_donation_person', true) : "-";
                $donation_reason = get_post_meta($value, 'fp_donation_reason_to_donate', true);
                $donation_url = get_post_meta($value, 'fp_donation_url', true);
                $page_id = url_to_postid($donation_url);
                $order = new WC_Order($value);
                $donated_reason = '<div class="reason_for">' . $donation_reason . '</div>';

                $data[] = array(
                    'sno' => $i,
                    'donarname' => sumo_donation_get_order_billing_first_name($order) . " " . sumo_donation_get_order_billing_last_name($order),
                    'email' => sumo_donation_get_order_billing_email($order),
                    'amount' => FP_DonationSystem_Main_Function::format_price($donatedamount),
                    'status' => sumo_donation_get_order_status($order),
                    'orderid' => '#' . $value,
                    'memorable' => ($reason_type == 'memorable') ? $donate_for_person : '-',
                    'honorable' => ($reason_type == 'Honorable') ? $donate_for_person : '-',
                    'reason' => $donation_reason ? $donated_reason : '-',
                    'url' => $donation_url ? get_the_title($page_id) . ' ' . $donation_url : '-',
                    'date' => sumo_donation_get_order_date($order),
                );
                $i ++;
            }
        }

        return $data;
    }

    public function column_id($item) {
        return $item['sno'];
    }

    public function column_default($item, $column_name) {

        switch ($column_name) {

            default:
                return $item[$column_name];
        }
    }

    function column_cb($item) {
        return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['orderid']
        );
    }

    function get_bulk_actions() {
        $actions = array(
            'delete' => __('Delete', 'donationsystem'),
        );
        return $actions;
    }

    function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            $get_list_orderids = FP_Donation_Product_Function::get_donated_order_ids();
            // var_dump($get_list_orderids);
            $count = count($ids);
            $remove_data = $ids;
            $difference = array_diff($get_list_orderids, $remove_data);
            //var_dump($difference);
            update_option('_fp_donated_order_ids', (array) $difference);
            $message = __($count . " rows deleted successfully");
            ?>
            <div id="message" class="updated"><p><?php echo $message ?></p></div>
            <?php
            //  exit();
        }
    }

    private function sort_data($a, $b) {

        $orderby = 'sno';
        $order = 'asc';

        if (!empty($_GET['orderby'])) {
            $orderby = $_GET['orderby'];
        }

        if (!empty($_GET['order'])) {
            $order = $_GET['order'];
        }

        $result = strnatcmp($a[$orderby], $b[$orderby]);

        if ($order === 'asc') {
            return $result;
        }

        return -$result;
    }

}
