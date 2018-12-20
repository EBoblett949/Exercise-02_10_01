<?php
    class OnlineStore {
        private $DBConnect = NULL;
        private $DBName = "";
        private $storeID = "";
        private $inventory = array();
        private $shoppingCart = array();

        function __construct() {
            include("inc_OnlineStoresDB.php");
            $this->DBConnect = $DBConnect;
            $this->DBName = $DBName;
        }
        
        function __destruct() {
            echo "<p>Closing database <em>$this->DBName</em>.</p>\n";
            $this->DBConnect->close();
        }

        function __wakeup() {
            include("inc_OnlineStoresDB.php");
            $this->DBConnect = $DBConnect;
            $this->DBName = $DBName;
        }

        public function setStoreID($storeID) {
            if ($this->storeID != $storeID) {
                $this->storeID = $storeID;
                $TableName = "inventory";
                $SQLstring = "SELECT * FROM " . $TableName . " WHERE storeID='" . $this->storeID . "'";
                $QueryResult = $this->DBConnect->query($SQLstring);
            if (!$QueryResult) {
                echo"<p>Unable to execute the query.<br> Error code " . $this->DBConnect->errno . ": " . $this->DBConnect->error."</p>\n";
                $this->storeID = "";
            }
            else {
                $inventory = array();
                $shoppingCart = array();
                while (($row = $QueryResult->fetch_assoc()) != NULL) {
                    $this->inventory[$row['productID']] = array();
                    $this->inventory[$row['productID']]['name'] = $row['name'];
                    $this->inventory[$row['productID']]['description'] = $row['description'];
                    $this->inventory[$row['productID']]['price'] = $row['price'];
                    $this->shoppingCart[$row['productID']] = 0;
                }
                // echo "<pre>\n";
                // print_r($this->inventory);
                // print_r($this->shoppingCart);
                // echo "</pre>\n";
                }
            }
        }
        public function getStoreInformation() {
            $retval = false;
            if ($this->storeID != "") {
                $TableName = "storeinfo";
                $SQLstring = "SELECT * FROM $TableName WHERE storeID='" . $this->storeID . "'";
                $QueryResult = $this->DBConnect->query($SQLstring);
                if ($QueryResult) {
                    $retval = $QueryResult->fetch_assoc();
                }
            }
            return $retval;
        }
        public function getProductList() {
            $retval = false;
            $subtotal = 0;
            if (count($this->inventory) > 0) {
                echo "<table width='100%'>";
                echo "<tr>\n";
                echo "<th>Product</th>";
                echo "<th>Price Each</th>";
                echo "<th># in Cart</th>";
                echo "<th>Total Price</th>";
                echo "<th>&nbsp;</th>";
                echo "<th>Description</th>";
                echo "</tr>\n";

                foreach ($this->inventory as $ID => $info) {
                    echo "<tr><td>" . htmlentities($info['name']) . "</td>";
                    echo "<td>" . htmlentities($info['description']) . "</td>";
                    printf("<td class='currency'>$%.2f</td>", $info['price']);
                    echo "<td class='currency'>" . $this->shoppingCart[$ID] . "</td>";
                    printf("<td class='currency'>$%.2f</td>", $info['price'] * $this->shoppingCart[$ID]);
                    echo "<td><a href='" . $_SERVER['SCRIPT_NAME'] . "?PHPSESSID=" . session_id() . "&ItemToAdd=$ID'>Add Item</a></td>";
                    $subtotal += ($info['price'] * $this->shoppingCart[$ID]);
                    echo "</tr>\n";
                }
                echo "<tr><td colspan='4'>Subtotal</td>";
                printf("<td class='currency'>$%.2f</td>", $subtotal);
                echo "<td>&nbsp;</td></tr>";
                echo "</table width='100%'>";
                $retval = true;
            }
            return($retval);
        }
        public function addItem() {
            $prodID = $_GET['ItemToAdd'];
            if (array_key_exists($prodID, $this->shoppingCart)) {
                $this->shoppingCart[$prodID] += 1;
            }
        }
    }
?>