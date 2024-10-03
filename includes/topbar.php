<header class="main-header">
    <nav class="navbar blue-bg navbar-static-top">
        <ul class="nav navbar-nav d-flex flex-row align-items-center">
            <li>
                <div class="brand inline">
                    <?php if ($_SESSION['Role'] != 'Sub-Center') {
                        if (!empty($dark_logo)) { ?>
                            <img src="<?= $dark_logo ?>" alt="logo" data-src="<?= $dark_logo ?>" data-src-retina="<?= $dark_logo_retina ?>" height="35" style="max-width:120px">
                        <?php }
                    } elseif ($_SESSION['Role'] == 'Sub-Center') {
                        $logo = $conn->query("SELECT Users.Photo FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Center = Users.ID WHERE Sub_Center = " . $_SESSION['ID'] . " AND Users.Photo != '/assets/img/default-user.png'");
                        if ($logo->num_rows > 0) {
                            $logo = $logo->fetch_assoc();
                        ?>
                            <img src="<?= $logo['Photo'] ?>" alt="center_logo" data-src="<?= $logo['Photo'] ?>" data-src-retina="<?= $logo['Photo'] ?>" height="35">
                    <?php }
                    } ?>

                </div>

                <!-- <?php
                        if ($_SESSION['Role'] != 'Sub-Center' && !empty($dark_logo)) {
                            $logoSrc = $dark_logo;
                        } elseif ($_SESSION['Role'] == 'Sub-Center') {
                            $logoQuery = $conn->query("SELECT Users.Photo FROM Center_SubCenter LEFT JOIN Users ON Center_SubCenter.Center = Users.ID WHERE Sub_Center = " . $_SESSION['ID'] . " AND Users.Photo != '/assets/img/default-user.png'");
                            if ($logoQuery->num_rows > 0) {
                                $logoData = $logoQuery->fetch_assoc();
                                $logoSrc = $logoData['Photo'];
                            }
                        }

                        $page = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                        $page = $page[1];

                        if (
                            isset($_SESSION['university_id']) &&
                            (!in_array($_SESSION['Role'], ['Administrator', 'Student']) ||
                                ($_SESSION['Role'] == 'Administrator' && $page == 'admissions') ||
                                ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/generate', '/leads/lead-details', '/academics/programs', '/academics/specializations', '/academics/departments']))
                            ) ||
                            ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/lists', '/leads/follow-ups', '/leads/lead-details']))
                        ) {
                            echo '<li class="brand inline">';
                            echo '<a href="javascript:;" id="notification-center" class="header-icon"';
                            if ($_SESSION['Role'] == 'Administrator' || (isset($_SESSION['Alloted_Universities']) && count($_SESSION['Alloted_Universities']) > 1)) {
                                echo ' onclick="changeUniversity()"';
                            }
                            echo '>';
                            echo '<img src="' . $logoSrc . '" alt="logo" data-src="' . $logoSrc . '" data-src-retina="' . $logoSrc . '" style="max-width:120px">';
                            echo '</a>';
                            echo '</li>';
                        } else {
                            echo '<img src="' . $logoSrc . '" alt="logo" data-src="' . $logoSrc . '" data-src-retina="' . $logoSrc . '" style="max-width:120px">';
                        }
                        ?> -->

            </li>
            <li>
                <a class="sidebar-toggle" data-toggle="push-menu" href="#"></a>
            </li>
            <!-- <?php
                    $page = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                    $page = $page[1];
                    if (isset($_SESSION['university_id']) && (!in_array($_SESSION['Role'], ['Administrator', 'Student']) || ($_SESSION['Role'] == 'Administrator' && $page == 'admissions') || ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/generate', '/leads/lead-details', '/academics/programs', '/academics/specializations', '/academics/departments']))) || ($_SESSION['Role'] == 'Administrator' && in_array($_SERVER['REQUEST_URI'], ['/users/centers', '/leads/lists', '/leads/follow-ups', '/leads/lead-details']))) { ?>
                <li class="brand inline">
                    <a href="javascript:;" id="notification-center" class="header-icon" <?php if ($_SESSION['Role'] == 'Administrator' || (isset($_SESSION['Alloted_Universities']) && count($_SESSION['Alloted_Universities']) > 1)) : echo 'onclick="changeUniversity()';
                                                                                        endif; ?>">
                        <img src="<?= $_SESSION['university_logo'] ?>" alt="logo" data-src="<?= $_SESSION['university_logo'] ?>" data-src-retina="<?= $_SESSION['university_logo'] ?>" style="max-width:120px">
                    </a>
                </li>
            <?php } ?> -->
        </ul>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav d-flex align-items-center flex-row">
                <div class="m-2">
                <?php if ($_SESSION['Role'] == 'Center' || $_SESSION['Role'] == 'Sub-Center') { 
                    //Total Amount

                    $amounts = $conn->query("SELECT sum(Amount) as total_amt FROM Wallets WHERE Added_By = " . $_SESSION['ID'] . " AND Status = 1");
                    $amounts = $amounts->fetch_assoc();
        
                    //Debit Amount
                    $debited_amount = 0;
                    $debit_amts = $conn->query("SELECT sum(Amount) as debit_amt FROM Wallet_Payments WHERE Added_By = " . $_SESSION['ID'] . " AND Type = 3");
                    if($debit_amts->num_rows > 0){
                    $debit_amt = $debit_amts->fetch_assoc();
                    $debited_amount = $debit_amt['debit_amt'];
                    }
                    
                    $amount = $amounts['total_amt'] - $debited_amount;
                ?>
                    <a href="#" class="btn btn-primary" style="padding: 7px 13px;" aria-label="" title="" data-toggle="tooltip" data-original-title="Available Balance"><?=$amount?><i class="uil uil-wallet"></i></a>
                    <a href="/accounts/wallet-payments" style="padding: 7px 13px;" class="btn btn-success" aria-label="" title="" data-toggle="tooltip" data-original-title="Add Amount"> <i class=" fa fa-plus "></i> </a>
                    <?php } ?>
                </div>
            </ul>
            <ul class="nav navbar-nav d-flex align-items-center flex-row">
                <li>
                    <?php if (($_SESSION['Role'] == 'Administrator' && $page == 'admissions') || ($_SESSION['Role'] == 'Administrator' && $_SERVER['REQUEST_URI'] == '/users/centers') || (isset($_SESSION['Alloted_Universities']) && count($_SESSION['Alloted_Universities']) > 1)) { ?>
                <li> <button class="btn btn-outline-primary d-none d-sm-none d-md-block mr-4 mt-2" onclick="changeUniversity()">Change Board</button></li>
            <?php } ?>
            </li>
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?= $_SESSION['Photo'] ?>" alt="User" data-src="<?= $_SESSION['Photo'] ?>" data-src-retina="<?= $_SESSION['Photo'] ?>" width="32" class="user-image" height="32">
                </a>
                <ul class="dropdown-menu">
                    <li class="">
                        <p class="mb-0 ml-3">Signed in as</p>
                        <p class="fw-bold ml-3 mb-0"><?= ucwords(strtolower($_SESSION['Name'])) ?></p>
                    </li>
                    <?php if ($_SESSION['Role'] != 'Student') { ?>
                        <li>
                            <a href="javascript:void(0);"><i class="fa fa-user"></i>Profile</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" onclick="changePassword(<?= $_SESSION['ID'] ?>)"><i class="fa fa-key"></i>Change Password</a>
                        </li>
                    <?php } else { ?>

                    <?php } ?>
                    <li>
                        <a href="/logout"><i class="fa fa-power-off"></i>Logout</a>
                    </li>
                </ul>
            </li>
            </ul>
        </div>
    </nav>
</header>