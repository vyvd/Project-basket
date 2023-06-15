<?php

use Carbon\Carbon;

class blumeReportingController extends Controller {
    public function dashboard()
    {
        $action = $this->get['action'];

        echo json_encode(['data' => $this->$action()]);
        exit();
    }

    /**
     * @param false $isCsv
     * @return array|string
     */
    public function salesReport($isCsv = false) {

        $date = $this->get['s_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) return $orders->findArray();

        return $orders->count();
    }

    /**
     * @param false $isCsv
     * @return array|string
     */
    public function salesPerCourse($isCsv = false)
    {
        $date = $this->get['ncpc_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select oi.courseID, count(oi.id), sum(oi.price) as total, c.title, oi.whenCreated, o.status from orderItems oi
            left join orders o on oi.orderID = o.id
            left join courses c on oi.courseID = c.id
            where o.status = 'completed'
              and oi.courseID > 0";

        if ($date) {
            $query .= " and oi.whenCreated >= '$date'";
        }

        $query .= " group by oi.courseID order by total desc";

        if (!$isCsv) $query .= " limit 10";

        $orders = ORM::For_Table('orderItems')->raw_query($query);

        return $orders->findArray();
    }

    /**
     * @param false $isCsv
     * @return array|string
     */
    public function revenuePerCustomer($isCsv = false)
    {
        $date = $this->get['rpacount_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select concat(a.firstname, ' ', a.lastname) as name, o.email, o.accountID, sum(o.total) as total, o.whenCreated, status
            from orders o
            left join accounts a on o.accountID = a.id
            where o.status = 'completed'
        ";

        if ($date) {
            $query .= " and o.whenCreated >= '$date'";
        }

        $query .= " group by o.accountID order by total desc";

        if (!$isCsv) $query .= " limit 10";

        $orders = ORM::For_Table('orders')->raw_query($query);

        return $orders->findArray();
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function salesReportValue($isCsv = false)
    {
        $date = $this->get['sv_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) {
            return $orders->findArray();
        }

        return '£' . number_format($orders->sum('total'),2);
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function certificateOrders($isCsv = false)
    {
        $date = $this->get['certord_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $result = ORM::For_Table("orderItems")->whereNotNull("certID");

        if ($date) {
            $result = $result->where_gte("whenCreated", $date);
        }

        if ($isCsv) {
            return $result->findArray();
        }

        $resultCount = clone $result;
        $resultCount = $resultCount->count();

        return ('£' . number_format($result->sum('price'),2)) . ' / ' . $resultCount;
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function avgSalesValue($isCsv = false)
    {
        $date = $this->get['avg_sv_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) return $orders->findArray();

        $countOrders = clone $orders;
        $value = $orders->sum('total') ? $orders->sum('total') / $countOrders->count(): 0;

        return '£' . (number_format($value,2));
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function repeatTransactionRate($isCsv = false)
    {
        $date = $this->get['repeattr_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $baseQuery = "
            select o.accountID, count(o.accountID) as countAccountID, sum(total) as total from orders o                
        ";

        if ($date) {
            $baseQuery .= " where o.status = 'completed' and whenCreated>='$date'";
        } else {
            $baseQuery .= " where o.status = 'completed'";
        }

        $query1 = $baseQuery . " group by o.accountID having countAccountID > 1";
        $query2 = $baseQuery;

        $ordersCountRepeat = ORM::For_Table("orders")->raw_query($query1)->findArray();
        $ordersCountAll = ORM::For_Table("orders")->raw_query($query2)->findOne();

        $ordersCountRepeat = count($ordersCountRepeat);
        $ordersCountAll = $ordersCountAll->countAccountID ?? 0;


        $value = $ordersCountRepeat / $ordersCountAll  * 100 ?? 0;

        if (is_nan($value)) $value = 0;

        if ($isCsv) return $ordersCountRepeat;

        return number_format($value,2) . '%';
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function completionRateSpend($isCsv = false)
    {
//        $date = $this->get['avg_sv_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

//        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");
        $totalSpend = ORM::For_Table("orders");

//        if ($date) {
//            $orders = $orders->where_gte("whenCreated", $date);
//        }

        if ($isCsv) return $orders->findArray();

        $value = $orders->sum('total') && $totalSpend->sum('total') ? $orders->sum('total') / $totalSpend->sum('total'): 0;

        return '£' . (number_format($value,2));
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function visitors($isCsv = false)
    {
        $date = $this->get['visitors'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");
        $customers = ORM::For_Table("orders")->where("status", "completed")->groupBy('accountID');

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) return $orders->findArray();

        $value = $orders->sum('total') / $customers->count() ?? 0;

        return  number_format($value,2);
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function unstartedCourses($isCsv = false)
    {
        $date = $this->get['uc_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) return $orders->findArray();

        return '£' . number_format($orders->sum('total'),2);
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function ltvByDate($isCsv = false)
    {
        $date = $this->get['ltvbd_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        if ($isCsv) return $orders->findArray();

        return '£' . number_format($orders->sum('total'),2);
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function completionRate($isCsv = false)
    {
        $date = $this->get['cr_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $result = ORM::For_Table("coursesAssigned")->where("completed", '1');

        if ($date) {
            $result = $result->where_gte("whenAssigned", $date);
        }

        if ($isCsv) return $result->findArray();

        return $result->count();
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function completionRatePerCourse($isCsv = false)
    {
        $date = $this->get['comRPC_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select c.id, c.title, c.price, count(ca.id) as rate
            from courses c";

        if ($date) {
            $query .= " left join coursesAssigned ca on ca.courseID = c.id and ca.whenAssigned >= '$date' and ca.completed = '1'";
        } else {
            $query .= " left join coursesAssigned ca on ca.courseID = c.id and ca.completed = '1'";
        }

        $query .= " group by c.id order by rate desc";

        if (!$isCsv) $query .= " limit 10";

        return ORM::For_Table("courses")->raw_query($query)->findArray();
    }

    /**
     * @param bool $isCsv
     * @return array|string
     */
    public function amountPaidForCourse($isCsv = false)
    {
        $date = $this->get['apfc_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
                select o.total*20/100 as tax, o.total*1.9/100 as fee, o.total, oi.courseID, c.title
                from courses as c
                left join orderItems oi on oi.courseID = c.id
                left join orders o on oi.orderID = o.id
                where total > 0";

//        if ($date) {
//            $query .= " where c.whenAdded >= '$date'";
//        }

        $query .= " order by o.total desc";

        if (!$isCsv) $query .= " limit 10";

        $orders = ORM::For_Table('orderItems')->raw_query($query);

        return $orders->findArray();
    }

    /**
     * @return array
     */
    public function bestSellingCourses($isCsv = false)
    {
        $date = $this->get['bsc_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "select count(oi.id) as count_sales, c.id, c.title
                from courses as c
                left join orderItems oi on oi.courseID = c.id
                left join orders o on oi.orderID = o.id";

        if ($date) {
            $query .= " and c.whenAdded >= '$date'";
        }

        $query .= " group by c.id order by count_sales desc";

        if (!$isCsv) $query .= " limit 10";

        $orders = ORM::For_Table('courses')->raw_query($query);

        return $orders->findArray();
    }

    /**
     * @return array
     */
    public function courseRating()
    {
        $date = $this->get['crs_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $result = ORM::For_Table("courses")->orderByDesc('averageRating');

        if ($date) {
            $result = $result->where_gte("whenAdded", $date);
        }

        $result = $result->limit(10);

        return $result->findArray();
    }

    /**
     * @return array
     */
    public function numberCustomersPerCourse($isCsv = false)
    {
        $date = $this->get['ncpc_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select count(accountID) as count_record, courseID, c.title
            from coursesAssigned ca
                left join courses c on ca.courseID = c.id";

        if ($date) {
            $query .= " where ca.whenAssigned >= '$date'";
        }

        $query .= " group by ca.courseID order by count_record desc";

        if (!$isCsv) $query .= " limit 10";

        $result = ORM::For_Table('courses')->raw_query($query);

        return $result->findArray();
    }

    /**
     * @return array
     */
    public function LTVByCourseFirstPurchased($isCsv = false)
    {
        $date = $this->get['ltvbcfp_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $result = ORM::For_Table("coursesAssigned")
            ->select(['courseID', 'accountID'])
            ->groupBy('coursesAssigned.courseID');

        if ($date) {
            $result = $result->where_gte("whenAssigned", $date);
        }

        if (!$isCsv) $result = $result->limit(10);

        return $result->findArray();
    }

    /**
     * @return array
     */
    public function numberCustomersPerCategory($isCsv = false)
    {
        $date = $this->get['ncpc_date'];
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select count(accountID) as count_record, courseID, cc.title
                from courseCategories as cc
                    left join courseCategoryIDs cCID on cc.id = cCID.category_id
                    left join coursesAssigned ca on cCID.course_id = ca.courseID
        ";

        if ($date) {
            $query .= " where ca.whenAssigned >= '$date'";
        }

        $query .= " group by cc.id order by count_record desc";

        if (!$isCsv) $query .= " limit 10";

        $result = ORM::For_Table('courses')->raw_query($query);

        return $result->findArray();
    }

    /**
     * @return array|float|int
     */
    public function usersUnstartedCourses($isCsv = false)
    {
        $isCsv = !$isCsv ? $this->get['is_csv'] : $isCsv;
//        $date = $this->get['crs_date'];

//        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $result = ORM::For_Table("coursesAssigned")->whereNull('lastAccessed');

//        if ($date) {
//            $result = $result->where_gte("whenAdded", $date);
//        }

        return $isCsv ? $result->findArray() : $result->count();
    }

    /**
     * @return array
     */
    public function revenuePerCourse($isCsv = false)
    {
        $date = $this->get['rpc_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $query = "
            select oi.courseID, count(oi.id), sum(oi.price) as total, c.title, c.whenAdded, oi.whenCreated, o.whenCreated as created_at
            from courses c";

        if ($date) {
            $query .= " left join orderItems oi on oi.courseID = c.id and oi.whenCreated >= '$date'";
        } else {
            $query .= " left join orderItems oi on oi.courseID = c.id";
        }

        $query .= " left join orders o on oi.orderID = o.id and o.status = 'completed' group by c.id order by total desc";

        if (!$isCsv) $query .= " limit 10";

        $orders = ORM::For_Table('courses')->raw_query($query)->findArray();

        return $orders;
    }

    /**
     * @return array
     */
    public function salesPerReferralCode()
    {
        $date = $this->get['sprc_date'];

        if (!$date) $date = Carbon::now()->format('Y-m-d');

        $orders = ORM::For_Table("orders")->where("status", "completed");

        if ($date) {
            $orders = $orders->where_gte("whenCreated", $date);
        }

        return $orders->findArray();
    }

    function downloadCSV()
    {
        $action = $_GET['action'];
        $date = $_GET['date'];

        $array = $this->$action(true, $date);

        return $this->array2csv($array);
    }

    function array2csv(array &$array, $fields = [])
    {
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'."data_export_" . date("Y-m-d") . ".csv".'";');

        if (count($array) == 0) {
            return null;
        }

        $headerFields = $fields ?: array_keys(reset($array));

        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, $headerFields);

        foreach ($array as $rows) {
            fputcsv($df, $rows);
        }
        fclose($df);
        ob_flush();
    }
}
