<div class="x-header">
    <nav>
        <ul>
            <li>
                <div class="item" panel="menu">
                    <i class="fa fa-list"></i>
                    Menu
                </div>
            </li>
            <li>
                <a class="item" href="index.html">
                    <i class="fa fa-home"></i>
                    Home
                </a>
            </li>
            <li>
                <?php if ( api()->login() ) { ?>
                    <div class="item" panel="user">
                        <i class="fa fa-user"></i>
                        Profile
                    </div>
                <?php } else { ?>
                    <div class="item" panel="login">
                        <i class="fa fa-user"></i>
                        Login
                    </div>
                <?php } ?>

            </li>
            <li>
                <a class="item" href="#">
                    <i class="fa fa-comment"></i>
                    Forum
                </a>
            </li>
            <li>
                <a class="item" href="#">
                    <i class="fa fa-gear"></i>
                    Settings
                </a>
            </li>
        </ul>
    </nav>
</div>
<div class="x-page <?php echo $x['name']?>">
    <div class="x-content">
        <?php include 'panel.php' ?>