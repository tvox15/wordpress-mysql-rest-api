<?php

/*
    To use, add the following lines in the spot you want to render this in:
    require ('path/to/this/file');
    $customSearch = new CustomSearch();
    if($customSearch->onCustomFunctionPage) {};
*/

class CustomSearch
{

    private $http_host = 'REMOVED';
    private $search_request_uri = '/custom-search/';
    private $results_request_uri = '/search-results/';
    private $edit_request_uri = '/edit-entries/';
    private $add_request_uri = '/add-entry/';
    private $tableName = "hackdata";
    private $realPassword = "test";

    function __construct()
    {
        if ($this->is_on_search_page()) {
            $this->onCustomFunctionPage = true;
            $this->displaySearchHTML();
        } else if ($this->is_on_results_page()) {
            $this->onCustomFunctionPage = true;
            $this->displayResultsHTML();
        } else if ($this->is_on_edit_page()) {
            $this->onCustomFunctionPage = true;
            $this->displayEditHTML();
        } else if ($this->is_on_add_page()) {
            $this->onCustomFunctionPage = true;
            $this->displayAddHTML();
        }
    }

    function getCurrentUri()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    function is_on_search_page()
    {
        return $this->getCurrentUri() === $this->http_host . $this->search_request_uri;
    }

    function is_on_results_page()
    {
        return $this->getCurrentUri() === $this->http_host . $this->results_request_uri;
    }

    function is_on_edit_page()
    {
        return $this->getCurrentUri() === $this->http_host . $this->edit_request_uri;
    }
    function is_on_add_page()
    {
        return $this->getCurrentUri() === $this->http_host . $this->add_request_uri;
    }

    function displayAddHTML()
    {
        $password = "";
        if (isset($_POST['password'])) {
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        }
        if (!isset($_POST['password'])) : ?>

            <form method="post" action="/add-entry/">
                <p>Enter password to add entry</p>
                <input type="hidden" name="id" value="<?= $_POST['id']; ?>">
                <input id="password" name="password">
                <input type="submit" id="submit" name="submit" />
            </form>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password === $this->realPassword && $_POST['add'] === 'add') : ?>
            <?php $this->submitAddEntry($_POST); ?>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password === $this->realPassword) : ?>
            <?php $this->displayAddEntry($_POST, $password); ?>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password !== $this->realPassword) : ?>
            <p>Incorrect password</p>
        <?php endif;
    }

    function submitAddEntry()
    {
        $con = $this->OpenCon();
        $sql = "INSERT INTO $this->tableName ( `Business`, `News URL`, `Type of Exploit`, `Industry`, `Business Type`, `Risk to Business`, `Risk Score to Business`, `Risk to Customers`, `Risk Score to Customers`, `Number of Customers impacted`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssssssss", $_POST['Business'], $_POST['NewsURL'], $_POST['TypeofExploit'], $_POST['Industry'], $_POST['BusinessType'], $_POST['RisktoBusiness'], $_POST['RiskScoretoBusiness'], $_POST['RisktoCustomers'], $_POST['RiskScoretoCustomers'], $_POST['NumberofCustomersimpacted']);
        if ($stmt->execute()) {
            echo 'Added succesfully! &nbsp; <a href="/add-entry/">Add another entry</a>';
        } else {
            printf("Error: %s.\n", $stmt->error);
            echo 'an error occured';
        }
    }


    function displayAddEntry($postVariables, $userPassword)
    {
        if (1 === 1) : ?>
            <h1>Add new entry:</h1>
            <form method="post" action="/add-entry/">
                <input type="hidden" name="password" value="<?= $userPassword; ?>">
                <input type="hidden" name="add" value="add">
                <div style="display: flex; flex-direction: column">
                    <label>Business: </label>
                    <input type="text" name="Business" value="<?= $postVariables['Business']; ?>">
                    <br>
                    <label>News URL: </label>
                    <input type="text" name="NewsURL" value="<?= $postVariables['NewsURL']; ?>"> <br>
                    <label>Type of Exploit: </label>
                    <input type="text" name="TypeofExploit" value="<?= $postVariables['TypeofExploit']; ?>"> <br>
                    <label>Industry: </label>
                    <input type="text" name="Industry" value="<?= $postVariables['Industry']; ?>"> <br>
                    <label>Business Type: </label>
                    <input type="text" name="BusinessType" value="<?= $postVariables['BusinessType']; ?>"> <br>
                    <label>Risk to Business: </label>
                    <textarea rows="10" name="RisktoBusiness"><?= $postVariables['RisktoBusiness']; ?></textarea>
                    <label>Risk Score to Business: </label>
                    <input type="text" name="RiskScoretoBusiness" value="<?= $postVariables['RiskScoretoBusiness']; ?>"> <br>
                    <label>Risk to Customers: </label>
                    <textarea rows="10" name="RisktoCustomers"><?= $postVariables['RisktoCustomers']; ?></textarea>
                    <label>Risk Score to Customers : </label>
                    <input type="text" name="RiskScoretoCustomers" value="<?= $postVariables['RiskScoretoCustomers']; ?>"> <br>
                    <label>Number of Customers impacted: </label>
                    <textarea rows="10" name="NumberofCustomersimpacted"><?= $postVariables['NumberofCustomersimpacted']; ?></textarea>
                    <input type="submit" id="submit" name="submit" />
                </div>
            </form>
            <script>
            jQuery(document).ready(function($) {
                    $("p, h1, h2, h3, h4, h5, h6, a, label").css({
                        "font-family": "lato"
                    });
            });
            </script>
        <?php endif;
    }

    function displayEditHTML()
    {
        $password = "";
        if (isset($_POST['password'])) {
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['password'])) : ?>

            <form method="post" action="/edit-entries/">
                <p>Enter password to edit entry</p>
                <input type="hidden" name="id" value="<?= $_POST['id']; ?>">
                <input id="password" name="password">
                <input type="submit" id="submit" name="submit" />
            </form>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password === $this->realPassword && $_POST['edit'] === 'edit') : ?>
            <?php $this->submitEditEntry(); ?>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password === $this->realPassword) : ?>
            <?php $this->displayEditEntry($_POST['id'], $password); ?>

        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $password !== $this->realPassword) : ?>

            <p>Incorrect password</p>

            <?php endif;
    }

    function displayEditEntry($id, $userPassword)
    {
        $con = $this->OpenCon();
        $sql = "SELECT * FROM $this->tableName WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $id);

        //  execute params
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            // iterate to push into array
            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) : ?>
                    <h1>Edit entry:</h1>
                    <form method="post" action="/edit-entries/">
                        <input type="hidden" name="password" value="<?= $userPassword; ?>">
                        <input type="hidden" name="edit" value="edit">
                        <div style="display: flex; flex-direction: column">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <br>
                            <label>Business: </label>
                            <input type="text" name="Business" value="<?= $row['Business']; ?>">
                            <br>
                            <label>News URL: </label>
                            <input type="text" name="NewsURL" value="<?= $row['News URL']; ?>"> <br>
                            <label>Type of Exploit: </label>
                            <input type="text" name="TypeofExploit" value="<?= $row['Type of Exploit']; ?>"> <br>
                            <label>Industry: </label>
                            <input type="text" name="Industry" value="<?= $row['Industry']; ?>"> <br>
                            <label>Business Type: </label>
                            <input type="text" name="BusinessType" value="<?= $row['Business Type']; ?>"> <br>
                            <label>Risk to Business: </label>
                            <textarea rows="10" name="RisktoBusiness"><?= $row['Risk to Business']; ?></textarea>
                            <label>Risk Score to Business: </label>
                            <input type="text" name="RiskScoretoBusiness" value="<?= $row['Risk Score to Business']; ?>"> <br>
                            <label>Risk to Customers: </label>
                            <textarea rows="10" name="RisktoCustomers"><?= $row['Risk to Customers']; ?></textarea>
                            <label>Risk Score to Customers : </label>
                            <input type="text" name="RiskScoretoCustomers" value="<?= $row['Risk Score to Customers']; ?>"> <br>
                            <label>Number of Customers impacted: </label>
                            <textarea rows="10" name="NumberofCustomersimpacted"><?= $row['Number of Customers impacted']; ?></textarea>
                            <input type="submit" id="submit" name="submit" />
                        </div>
                    </form>
                    <script>
            jQuery(document).ready(function($) {
                    $("p, h1, h2, h3, h4, h5, h6, a, label").css({
                        "font-family": "lato"
                    });
            });
            </script>
            <?php endwhile;
            } else {
                echo 'id doesnt match';
            }
        }
    }

    function submitEditEntry()
    {
        $con = $this->OpenCon();
        $sql = "UPDATE $this->tableName SET `Business`=?, `News URL`=?, `Type of Exploit`=?, `Industry`=?, `Business Type`=?, `Risk to Business`=?, `Risk Score to Business`=?, `Risk to Customers`=?, `Risk Score to Customers`=?, `Number of Customers impacted`=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssssssssi", $_POST['Business'], $_POST['NewsURL'], $_POST['TypeofExploit'], $_POST['Industry'], $_POST['BusinessType'], $_POST['RisktoBusiness'], $_POST['RiskScoretoBusiness'], $_POST['RisktoCustomers'], $_POST['RiskScoretoCustomers'], $_POST['NumberofCustomersimpacted'], $_POST['id']);
        if ($stmt->execute()) {
            echo 'Updated succesfully';
        } else {
            printf("Error: %s.\n", $stmt->error);
            echo 'an error occured';
        }
    }

    function displaySearchHTML()
    {
        $this->displayForm();
    }

    function displayForm()
    {
        if (true) : ?>
            <!-- your inputs -->
            <div class="search-form">
                <h1>Search Hacks:</h1>
                <form method="post" action="/search-results/">
                    <div class="search-bar-wrapper">
                        <input type="text" name="searchTerm" id="searchTerm" />
                        <input type="submit" id="submitSearch" value="Submit" name="submit" />
                        <p id="searchResults" class="status"></p>
                    </div>
                    <div class="filter-wrapper">
                        <div>
                            <p>Filter:</p>
                        </div>
                        <div>
                            <select name="filter" id="filter">
                                <option value="all">All</option>
                                <option value="Type of Exploit">Exploit</option>
                                <option value="Industry">Industry</option>
                                <option value="Business Type">Business</option>
                            </select>
                        </div>
                    </div>
                </form>
                <a href="/add-entry/">Add a new entry</a>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    $("#filter").val('all');
                    $("p, h1, h2, h3, h4, h5, h6, a").css({
                        "font-family": "lato"
                    });
                    $(".search-form").css({
                        "display": "flex",
                        "width": "100%",
                        "flex-direction": "column",
                        "text-align": "center",
                        "justify-content": "center",
                    });
                    $(".search-bar-wrapper").css({

                        "width": "100%",
                        "text-align": "center",
                    });
                    $(".filter-wrapper").css({
                        "width": "100%",
                        "justify-content": "center",
                        "text-align": "center",
                    });
                    $(".filter-wrapper p").css({
                        "margin-bottom": "2px",
                    });
                    $("#filter").css({
                        "margin-bottom": "5px",
                    });




                    $("#filter").on('change', function() {
                        var filter = $("#filter option:selected").val();
                        console.log(filter);

                        function removeSubFilter() {
                            var count = $(".filter-wrapper div").length;
                            console.log(count);
                            if (count === 3 && filter !== 'all') {
                                $(".filter-wrapper").children().last().remove();
                            }
                        }
                        if (filter === 'all') {
                            $(".filter-wrapper").children().last().remove();
                        } else {

                            // do something different based on which filter
                            if (filter === 'Type of Exploit') {
                                removeSubFilter();
                                $(".filter-wrapper").append(`
                           <div>
                           <select name="filter-options" id="filter-options">
                                <?php $this->getFilterValues('exploit') ?>
                                </select>
                            </div>
                           `);


                            } else if (filter === 'Industry') {
                                removeSubFilter();
                                $(".filter-wrapper").append(`
                           <div>
                            <select name="filter-options" id="filter-options">
                                <?php $this->getFilterValues('industry') ?>
                                </select>
                            </div>
                           `);
                            } else if (filter === 'Business Type') {
                                removeSubFilter();
                                $(".filter-wrapper").append(`
                           <div>
                            <select name="filter-options" id="filter-options">
                                <?php $this->getFilterValues('business') ?>
                                </select>
                            </div>
                           `);
                            }


                        }
                    })
                })
            </script>
            <?php endif;
    }

    function getFilterValues($filterType)
    {
        $con = $this->OpenCon();

        switch ($filterType) {
            case 'exploit':
                $sql = "SELECT DISTINCT `Type of Exploit` FROM $this->tableName";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) : ?>
                        <option value="<?= $row['Type of Exploit']; ?>"><?= $row['Type of Exploit']; ?></option>
                    <?php endwhile;
                } else {
                    echo 'error';
                }
                break;

            case 'industry':
                $sql = "SELECT DISTINCT `Industry` FROM $this->tableName";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) : ?>
                        <option value="<?= $row['Industry']; ?>"><?= $row['Industry']; ?></option>
                    <?php endwhile;
                } else {
                    echo 'error';
                }
                break;

            case 'business':
                $sql = "SELECT DISTINCT `Business Type` FROM $this->tableName";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    // output data of each row
                    while ($row = $result->fetch_assoc()) : ?>
                        <option value="<?= $row['Business Type']; ?>"><?= $row['Business Type']; ?></option>
            <?php endwhile;
                } else {
                    echo 'error';
                }
                break;
        }
    }



    function displayResultsHTML()
    {
        $this->searchDb();
    }

    function OpenCon()
    {
        require('wp-config.php');
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        // Check connection
        if ($mysqli->connect_errno) {
            echo "Failed to connect to MySQL: " . $mysqli->connect_error;
            exit();
        } else {
            return $mysqli;
        }
    }

    function searchDb()
    {
        $searchTerm = $_POST['searchTerm'];
        $mainFilter = $_POST['filter'];
        $subfilter = $_POST['filter-options'];
        $con = $this->OpenCon();
        $sql = "";

        // array to bind in mysql statement
        $arrayToBind = ["%" . $searchTerm . "%", "%" . $searchTerm . "%"];
        // types to bind in mysql statement
        $typesToBind = "ss";

        $sql = "SELECT * FROM $this->tableName WHERE (`Risk to Business` LIKE ? OR `Risk to Customers` LIKE ?)";
        if (isset($subfilter)) {
            // change array and types if filter is set

            $sanitizedMainFilter = filter_var($mainFilter, FILTER_SANITIZE_STRING);
            $sanitizedSubFilter = filter_var($subfilter, FILTER_SANITIZE_STRING);
            $sql .=  " AND `$sanitizedMainFilter`='$sanitizedSubFilter'";
        }

        if ($stmt = $con->prepare($sql)) {

            if ($stmt->bind_param($typesToBind, ...$arrayToBind)) {

                //  execute params
                if ($stmt->execute()) {
                    $result = $stmt->get_result();
                    // iterate to push into array


                    if ($result->num_rows > 0) {
                        // output data of each row
                        $this->displayResults($result);
                    } else {
                        echo '<div><h3>No Results Found. &nbsp;<a href="/custom-search">Search again</a></h3></div>';
                    }
                } else {
                    echo 'exec error';
                }
            } else {
                echo 'error';
            }
        } else {
            echo 'prepare error';
        }
    }

    function displayResults($result)
    {
        echo $this->customCss();
        echo '<div style="display: flex; justify-content: space-between;">
        <h1>Search Results:</h1>
        <h3 style="margin-top: 20px;">
        <a href="/custom-search/">back to search page</a>
        </h3>
        </div>
        <hr>
        <div class="table">';
       

        while ($row = $result->fetch_assoc()) : ?>
            <div class="entry-wrapper">
                <div class="row">

                    <div class="table-header">
                        <p >Business:</p>
                    </div>
                    <div class="body-data">
                        <p> <?= $row["Business"]; ?></p>
                        <p><a href="<?= $row['News URL']; ?>" target="_blank">&nbsp;&nbsp; (read more)</a></p>
                    </div>
                </div>
                <div class="row">
                    <div class="table-header">
                        <p>Type of Exploit:</p>
                    </div>
                    <div class="body-data">
                        <p><?= $row["Type of Exploit"]; ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="table-header">
                        <p>Industry:</p>
                    </div>
                    <div class="body-data">
                        <p><?= $row["Industry"]; ?> &nbsp; (<?= $row["Business Type"]; ?>)</p>
                    </div>
                </div>
                <div class="row">
                    <div class="column">
                        <div class="column-header">
                            <div class="column-header-data">
                                <p><b>Risk to Business</b></p>
                            </div>
                            <div class="column-header-score">
                                <p>Risk Score: &nbsp;<b style="color: <?php $this->setScoreColor($row["Risk Score to Business"]); ?>"><?= $row["Risk Score to Business"]; ?></b> </p>
                            </div>
                        </div>
                        <div class="column-data">
                            <p><?= $row["Risk to Business"]; ?></p>

                        </div>
                    </div>
                    <div class="column">
                        <div class="column-header">
                            <div class="column-header-data">
                                <p><b>Risk to Customers</b></p>
                            </div>
                            <div class="column-header-score">
                                <p>Risk Score: &nbsp;<b style="color: <?php $this->setScoreColor($row["Risk Score to Customers"]); ?>"><?= $row["Risk Score to Customers"]; ?></b> </p>
                            </div>
                        </div>
                        <div class="column-data">
                            <p><?= $row["Risk to Customers"]; ?></p>
                            <div class="top-border">
                                <p class="low-bottom-margin"><b>Number of Customers impacted:</b></p>
                                <p><?= $row["Number of Customers impacted"]; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="edit-button-wrapper">
                    <form method="POST" action="/edit-entries/">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <button type="submit">Edit</button>
                    </form>
                </div>
            </div>
            </hr>
        <?php endwhile;
        echo ' </div>';
    }

    function setScoreColor($score)
    {
        switch ($score) {
            case "Moderate":
                echo 'green';
                break;
            case "Severe":
                echo 'orange';
                break;
            case "Extreme":
                echo 'red';
                break;
        }
    }

    function customCss()
    {
        if (1 === 1) : ?>
            <script>
                jQuery(document).ready(function($) {
                    $("p, h1, h2, h3, h4, h5, h6, a").css({
                        "font-family": "lato"
                    });
                    $(".table").css({
                        "display": "flex",
                        "width": "100%",
                        "text-align": "right",
                        "justify-content": "center",
                        "flex-direction": "column",

                    });
                    $(".entry-wrapper").css({
                        "border-bottom": "solid black 4px",
                        "padding-bottom": "20px",
                        "padding-top": "20px",
                    });

                    $(".row").css({
                        "display": "flex",
                        "width": "100%",
                    });

                    $(".table-header").css({
                        "width": "10%"


                    });
                    $(".body-data").css({
                        "display": "flex",
                        "width": "90%",
                        "padding-left": "10px",
                    });
                    $(".column").css({
                        "display": "flex",
                        "width": "50%",
                        "flex-direction": "column",
                        "padding": "10px",
                        "border": "solid 1px black"

                    });
                    $(".column-header").css({
                        "width": "100%",
                        "text-align": "left",
                        "display": "flex",
                        "justify-content": "space-between",
                        "margin-bottom": "8px"
                    });
                    $(".column-header-data").css({
                        "width": "100%",
                        "text-align": "left",
                        "border-bottom": "solid 1px black",
                        "margin-bottom": "8px"
                    });
                    $(".column-header-score").css({
                        "width": "100%",
                        "text-align": "right",
                        "border-bottom": "solid 1px black",
                        "margin-bottom": "8px"
                    });
                    $(".column-header p").css({
                        "margin-bottom": "2px"
                    });
                    $(".column-data").css({
                        "display": "flex",
                        "width": "100%",
                        "text-align": "left",
                        "flex-direction": "column",
                    });
                    $(".top-border").css({
                        "border-top": "solid 1px black",
                        "padding-top": "8px"
                    });
                    $(".low-bottom-margin").css({
                        "margin-bottom": "2px"
                    });
                    $(".edit-button-wrapper").css({
                        "margin-top": "10px"
                    });

                });
            </script>
<?php endif;
    }
}


?>