<?php
    class Product {
        private $conn;


        function __construct($db){
            $this->conn = $db;
        }

        public function getList($currentPage = 1, $limit = 12,$categoryID = -1) { 
            $resultList = array();
            $resultList["data"] = [];
            $totalQuery = '';
            if($categoryID == -1) {
               $totalQuery =  'SELECT count(ProductID) as Soluong from fo_product';
            }  else {
                $totalQuery = 'SELECT count(ProductID) as Soluong from fo_product p WHERE p.CategoryID = '.$categoryID.' ';
            }
            $totalResult = mysqli_query($this->conn,$totalQuery);
            $row = mysqli_fetch_assoc($totalResult);
            $totalProduct = $row["Soluong"];
            $totalPage = ceil($totalProduct / $limit);
            $query = 'call getListProduct('.($currentPage - 1) * $limit.','.$limit.','.$categoryID.')' ; // pagination hàm nhận 3 tham số currentPage,limit,categoryID
            $data = mysqli_query($this->conn,$query);
            if(!$data || mysqli_num_rows($data) > 0) {
                    while($row1 = mysqli_fetch_array($data)) {
                        $items = array (
                            "product_id" => $row1["ProductID"],
                            "name" => $row1["Name"],
                            "price" => $row1["Price"],
                            "description" => $row1["Description"],
                            "discount" => $row1["discount"],
                            "image" => $row1["Image"],
                            "categoryName" => $row1["ca_name"],
                            "categoryID" => $row1["CategoryID"],
                            "createdAt" => $row1["create_At"],
                            "dis_play" => $row1["dis_play"]
                        );
                        array_push($resultList["data"],$items);
                    }
            }
            $resultList["total_page"] = $totalPage;
            $resultList["current_page"] = (int) $currentPage;
            mysqli_close($this->conn);
            return $resultList;
        }

        public function getDetail($productID) {
            $result = [];
            $result["product"] = [];
            $result["review"] = [];
            $queryProduct = "SELECT *,Ca_Name from fo_product p, fo_category c where p.CategoryID = c.CategoryID and  p.ProductID =  ".$productID."";
            $resultQueryProduct = mysqli_query($this->conn, $queryProduct);
            if(mysqli_num_rows($resultQueryProduct) > 0) {
                while($row = mysqli_fetch_assoc($resultQueryProduct)) {
                    $item = array (
                        "product_id" => $row["ProductID"],
                        "name" => $row["Name"],
                        "price" => $row["Price"],
                        "description" => $row["Description"],
                        "discount" => $row["discount"],
                        "image" => $row["Image"],
                        "createdAt" => $row["create_At"],
                        "display" => $row["dis_play"],
                        "categoryID" => $row["CategoryID"],
                        "category" => $row["Ca_Name"],
                    );
                    array_push($result["product"],$item);
                };
            }
            // $queryReview = "SELECT * from FO_Review r,FO_User u where r.productID = ".$productID." and r.userID = u.userID";
            // $resultReview = mysqli_query($this->conn,$queryReview);
            // var_dump(mysqli_num_rows($resultReview));
            // if(mysqli_num_rows($resultReview) > 0) {
            //     while($row = mysqli_fetch_assoc($resultReview)) {
            //         $item = array (
            //             "review_id" => $row["RvID"],
            //             "content" => $row["Rv_Content"],
            //             "createdAt" => $row["createdAt"],
            //             "stars" => $row["Rv_Stars"],
            //             "userName" => $row["us_name"],
            //             "userImage" => $row["us_image"]
            //         );
            //         array_push($result["review"],$item);
            //     }
            // }
            mysqli_close($this->conn);
            return $result;
        }

        public function create($name,$price,$description,$discount,$image,$categoryID,$display) {
            $query="INSERT INTO `fo_product` (Name, Price, Description, Discount, CategoryID, Image,dis_play) VALUES('".$name."','".$price."','".$description."','".$discount."','".$categoryID."','".$image."', '".$display."')";
            $result = mysqli_query($this->conn,$query);
            mysqli_close($this->conn);
            return $result;
        }

        public function update($productID,$name,$price,$description,$discount,$image,$categoryID,$display) {
            $query = 'UPDATE fo_product
                        SET Name = "'.$name.'", Price = '.$price.', Description = "'.$description.'", discount = '.$discount.',CategoryID = '.$categoryID.',dis_play = '.$display.', Image = "'.$image.'"
                        WHERE ProductID = "'.$productID.'"';
            $result = mysqli_query($this->conn,$query);
            mysqli_close($this->conn);
            return $result;
        }
        public function delete($productID) {
            $query = 'DELETE from `fo_product` where ProductID = '.$productID.' ';
            $result = mysqli_query($this->conn,$query);
            mysqli_close($this->conn);
            return $result;
        }
        public function getRelatedProduct($categoryID) {
            $res = [];
            $res["data"] = [];
            $query = 'call getRelatedProduct('.$categoryID.')';
            $result = mysqli_query($this->conn,$query);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $item = array (
                        "product_id" => $row["ProductID"],
                        "name" => $row["Name"],
                        "price" => $row["Price"],
                        "description" => $row["Description"],
                        "discount" => $row["discount"],
                        "image" => $row["Image"],
                        "createdAt" => $row["create_At"]
                    );
                    array_push($res["data"],$item);
                }
                $res["status"] = true;
            }
            return $res;
        }
    }

?>