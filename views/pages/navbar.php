<style type="text/css">
    @media (min-width: 990px) {
        /*
        nav.navbar{
            display: none;
        }
        */
    }
</style>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item" style="margin-right:8px">
            <img src="../../../icon/icon_dmi_white.png" style="width:35px" alt="DMI Logo" class="brand-image elevation-3 img-circle">
        </li>
        <li class="nav-item" style="display:flex;justify-content: center;align-items: flex-end;flex-direction: row;">
            <h5>Software | DMI</h5>
        </li>
    </ul>

    <?php
        $cantidadAlertas =  count(Template::$alertas);
    ?>

    <?php if ($cantidadAlertas > 0 ) : ?>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell" style="font-size:30px"></i>
                    <span class="badge badge-danger navbar-badge"  style="font-size:15px"><?php echo $cantidadAlertas; ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header"><?php echo $cantidadAlertas; ?> Notificación(es)</span>
                    <div class="dropdown-divider"></div>
                    <?php foreach(Template::$alertas as $alerta ) : ?>
                        <a href="<?php echo $alerta["url"] ?>" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> <?php echo $alerta["mensaje"] ?>
                        </a>
                        <div class="dropdown-divider"></div>
                    <?php endforeach; ?>
                    <!-- <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>  -->
                </div>
            </li>
        </ul>
    <?php endif; ?>

    
    
</nav>