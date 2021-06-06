        <!--Footer-->
        <footer class="bg-dark text-white">
            <div class="container">
                <div class="row d-flex align-items-center p-3">
                    <div class="footer-logo d-flex justify-content-sm-start justify-content-center col-lg-4 col-md-6 col-sm-6 col-12">
                        <a href="index.php"><img alt="" class="mt-0 footer-logo" src="./assets/logo.png"/></a>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 d-flex justify-content-lg-center justify-content-sm-end justify-content-center align-items-center">
                        <ul class="p-0 m-0 d-flex justify-content-center social">
                            <li>
                                <a href="https://www.facebook.com/nguyencaonhan2712">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://github.com/nguyencaonhan271201">
                                    <i class="fab fa-github"></i>
                                </a>
                            </li>
                            <li>
                                <a href="https://youtu.be/Ws-QlpSltr8">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 d-flex justify-content-lg-end justify-content-center">
                        <?php 
                            echo "© Nguyen Cao Nhan ". date("Y");
                        ?>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
        <?php if($site_name != "create" && $site_name != "edit"):?>
        <script>
            function check() {
                let viewheight = window.innerHeight;
                let nav_height = document.querySelector('.navbar').scrollHeight;
                let main_height = document.querySelector('#main-container').scrollHeight;
                let foot_height = document.querySelector('footer').scrollHeight;
                let total_height = main_height + foot_height + nav_height;
                //Fixed the footer to bottom in case the 
                if (total_height > viewheight) {
                    if (document.querySelector('footer').classList.contains('fixed-bottom')) {
                        document.querySelector('footer').classList.remove('fixed-bottom');
                    }
                } else {
                    if (!document.querySelector('footer').classList.contains('fixed-bottom')) {
                        document.querySelector('footer').classList.add('fixed-bottom');
                    }
                }    
            }

            window.addEventListener('resize', function() {
                check();
            });
        </script>
        <?php endif; ?>
        <?php 
            if ($site_name != "create" && $site_name != "edit") {
                if ($site_name == "post") {
                    echo "<script>
                        setTimeout(() => {
                            check();
                        }, 1000);
                        setTimeout(() => {
                            check();
                        }, 2000);
                    </script>";
                } else {
                    echo "<script>check();</script>";
                }
            }
        ?>
    </body>
</html>