
<div class="panel widget" name="menu">
    <h2>MENU</h2>
    <ul>
        <li>Menu 1</li>
        <li>Menu 1</li>
        <li>Menu 1</li>
        <li>Menu 1</li>
        <li>Menu 5</li>
        <li class="panel-close">Close</li>
    </ul>
</div>

<?php if ( $user = api()->login() ) { ?>
    <div class="panel widget" name="user">
        <h2><?php echo $user->nicename?></h2>
        <ul>
            <li>nickname: <?php echo $user->nickname?></li>
            <li><div class="logout">Logout</div></li>
        </ul>
    </div>
<?php } else { ?>
    <div class="panel widget" name="login">
        <h2>User Login</h2>
        <div class="form login">
            <form>
                <input type="hidden" name="forum" value="user_login_check">
                <input type="hidden" name="response" value="ajax">
                <input type="text" name="user_login" value="" placeholder="Input User ID">
                <input type="password" name="user_pass" value="" placeholder="Input Password">
                <div class="message loader"></div>
                <div class="button">
                    <button type="button" class="login-submit btn btn-secondary btn-sm">SUBMIT</button>
                    <button type="button" class="login-cancel panel-close btn btn-secondary btn-sm">CANCEL</button>
                </div>
            </form>
        </div>
    </div>
<?php  } ?>