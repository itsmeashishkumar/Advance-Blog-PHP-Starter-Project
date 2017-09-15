<!-- Including Header PHP -->
<?php include "includes/header.php"; ?>
    <?php include "includes/db.php"; ?>
        <!-- Navigation -->
        <?php include "includes/navigation.php"; ?>
            <!-- Page Content -->
            <div class="container">
                <div class="row">
                    <!-- Blog Entries Column -->
                    <div class="col-md-8">
                        <?php
            if(isset($_GET['p_id'])){
                $the_post_id = $_GET['p_id'];
                $message = '';
                 $query = "UPDATE posts SET post_views_count = post_views_count+1 WHERE post_id = $the_post_id ";
                 $update_post_views_count = mysqli_query($connection, $query);
                if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'){
                    $query = "SELECT * FROM posts WHERE post_id = $the_post_id";
                } else {
                    $query = "SELECT * FROM posts WHERE post_id = $the_post_id AND post_status = 'publish' ";
                }
            $select_all_posts_query = mysqli_query($connection,$query);
                if(mysqli_num_rows($select_all_posts_query) < 1){
                    echo "<center><div class='alert alert-info'><strong>Sorry!</strong>Post not abilable.....</div></center>";
                } else{
            while($row = mysqli_fetch_assoc($select_all_posts_query)){
                $post_title = $row['post_title'];
                $post_author = $row['post_author'];
                $post_date = $row['post_date'];
                $post_image = $row['post_image'];
                $post_content = $row['post_content'];
            ?>
                            <!-- First Blog Post -->
                            <h2>
                    <a href="#">
                        <?php echo $post_title; ?> </a>
                </h2>
                            <p class="lead"> by
                                <a href="author_post.php?author=<?php echo $post_author; ?>">
                                    <?php echo $post_author; ?>
                                </a>
                            </p>
                            <p><span class="glyphicon glyphicon-time"></span> Posted on
                                <?php echo $post_date; ?>
                            </p>
                            <hr> <img class="img-responsive" src="images/post_pic/<?php echo $post_image; ?>" alt="">
                            <hr>
                            <p>
                                <?php echo $post_content; ?>
                            </p>
                            <hr>
                            <?php }
                 ?>
                                <!-- Blog Comments -->
                                <!-- Comments Form -->
                                <?php
                    if(isset($_POST['create_comment'])){
                        $comment_post_id = $_GET['p_id'];
                        $comment_author = $_POST['comment_author'];
                        $comment_email = $_POST['comment_email'];
                        $comment_content = $_POST['comment_content'];
                        date_default_timezone_set("Asia/Dhaka");
                        $comment_date = date('D, F d, Y - h:i:s A');
                        if(!empty($comment_author) && !empty($comment_email) && !empty($comment_content)){
                        $query = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date) ";
                            $query .= "VALUES($comment_post_id, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unapproved', '{$comment_date}')";
                        $comment_create_query = mysqli_query($connection, $query);
                        if(!$comment_create_query){
                            die('Faild' . mysqli_error($connection));
                        } else {
                            $message = "Comment added, waiting for admin aproval.";
                        }
                        // Comment count updating query
                        $query = "UPDATE posts SET post_comment_count = post_comment_count+1 WHERE post_id = $comment_post_id ";
                        $update_comment_count = mysqli_query($connection, $query);
                        }
                        else {
                            echo "<script>alert('Fields can not be empty!')</script>";
                        }
                    }
                      ?>
                                    <div class='well'>
                                        <?php
                        if($message !== ''){
                            echo "<div class='alert alert-success'><strong>Comment Added!</strong> Waiting for Admin approvel action. </div>";
                        }
                        ?>
                                            <?php
                        if(isset($_SESSION['username'])){
                            $user_name = $_SESSION['username'];
                            $user_email = $_SESSION['email'];
                            echo "<h4>Leave a Comment:</h4>
                        <form role='form' action='' method='post'>
                            <div class='form-group'><label for='author'>Author Name: </label><br>
                                <input class='form-control' type='hidden' name='comment_author' value='$user_name'>$user_name
                            </div>
                            <div class='form-group'>
                                <label for='email'>Email: </label><br>
                                <input class='form-control' type='hidden' name='comment_email' value='$user_email'>$user_email
                            </div>
                            <div class='form-group'>
                                <label for='comment'>Comment</label><br>
                                <textarea class='form-control' rows='3' name='comment_content'></textarea>
                            </div>
                            <button type='submit' class='btn btn-primary' name='create_comment'>Submit</button>
                        </form>"; }
                        else {
                            echo "<h4>Leave a Comment:</h4><br><div class='alert alert-warning'><center><strong>Sorry! </strong>Your are not a logged user!!<br> Please <a href='login.php'>Login</a> or <a href='registration.php'>Register</a></center></div>";
                        }
                        ?> </div>
                                    <hr>
                                    <!-- Posted Comments -->
                                    <?php
                    $query = "SELECT * FROM comments WHERE comment_post_id = $the_post_id AND comment_status = 'approve' ORDER BY comment_id DESC";
                    $select_comment_query = mysqli_query($connection,$query);
                    while($row = mysqli_fetch_array($select_comment_query)){
                    $comment_author = $row['comment_author'];
                    $comment_content = $row['comment_content'];
                    $comment_reply = $row['comment_reply'];
                    $comment_reply_date = $row['comment_reply_date'];
                    $comment_date = $row['comment_date'];
                       ?>
                                        <!-- Comment -->
                                        <div class="media">
                                            <div class="media-left"> <img src="/images/user_pic/no_avatar.gif" class="media-object" style="width:45px"> </div>
                                            <div class="media-body">
                                                <h4 class="media-heading">
                                    <?php echo "<b>$comment_author</b>"; ?><small><i> (Posted on <?php echo $comment_date; ?>)</i></small></h4>
                                                <p>
                                                    <?php echo $comment_content; ?>
                                                </p>
                                                <?php
                                                if($comment_reply !== ''){
                                                    echo "<div class='media'>
                                                        <div class='media-left'> <img src='images/user_pic/img_avatar2.png' class='media-object' style='width:45px'> </div>
                                                        <div class='media-body'>
                                                        <h4 class='media-heading'><b>Administitor</b> <small><i>Posted on $comment_reply_date</i></small></h4>
                                                        <p>{$comment_reply}</p>
                                                            </div>
                                                            </div>";
                                                }
                                                ?> </div>
                                        </div>
                                        <?php } }
            } else {
                header("Location: index.php");
            } ?>
                    </div>
                    <!-- Blog Sidebar Widgets Column -->
                    <?php include "includes/sidebar.php"; ?>
                </div>
                <!-- /.row -->
                <hr>
                <!-- Including Footer PHP -->
                <?php include "includes/footer.php"; ?>