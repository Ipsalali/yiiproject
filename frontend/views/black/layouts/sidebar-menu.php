<?php
use yii\helpers\Url;
use yii\helpers\Html;

$active_route = $this->context->route; 
?>
<div class="sidebar" data-color="purple" data-background-color="black" data-image="templates/material-dark/img/sidebar-2.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
      <div class="logo">
        <a href="/" class="simple-text logo-normal">
          TED CRM
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">

          <?php if(Yii::$app->user->identity->role->name != "client"){ ?>
            
            <!-- <li class="nav-item ">
                <a class="nav-link <?php echo $active_route == '/' ? 'active' : '';?>" href="/">
                  <i class="material-icons">dashboard</i>
                  <p>Главная</p>
                </a>
            </li> -->


            <?php  if(Yii::$app->user->can("autotruck/index")){ ?>
              <li class="nav-item <?php echo $active_route == 'autotruck/index' ? 'active' : '';?>">
                  <a class="nav-link" href="<?php echo Url::to(['/autotruck/index']);?>">
                    <i class="material-icons">dashboard</i>
                    <p>Заявки</p>
                  </a>
              </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("client/index")){ ?>
              <li class="nav-item <?php echo $active_route == 'client/index' ? 'active' : '';?>">
                  <a class="nav-link" href="<?php echo Url::to(['/client/index']);?>">
                    <i class="material-icons">person</i>
                    <p>Клиенты</p>
                  </a>
              </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("autotruck/report")){ ?>
              <li class="nav-item dropdown">
                  <a class="nav-link" href="#">
                    <i class="material-icons">content_paste</i>
                    <p class=" dropdown-toggle">Отчеты</p>
                  </a>
                  <ul class="nav nav-second-level"  style="display:<?php echo $active_route == 'autotruck/report' || $active_route == 'site/org-report' ? "block" : "none"; ?>">
                      <li class="nav-item <?php echo $active_route == 'autotruck/report' ? 'active' : '';?>">
                        <a class="nav-link" href="<?php echo Url::to(['/autotruck/report']);?>">
                          <i class="material-icons">content_paste</i>
                          <p>Отчет</p>
                        </a>
                      </li>

                      <li class="nav-item <?php echo $active_route == 'site/org-report' ? 'active' : '';?>">
                        <a class="nav-link" href="<?php echo Url::to(['/site/org-report']);?>">
                          <i class="material-icons">content_paste</i>
                          <p>Отчет по организациям</p>
                        </a>
                      </li>
                  </ul>
              </li>
            <?php } ?>
            

            <?php  if(Yii::$app->user->can("site/sverka")){ ?>
              <li class="nav-item <?php echo $active_route == 'sverka/index' ? 'active' : '';?>">
                  <a class="nav-link" href="<?php echo Url::to(['/sverka/index']);?>">
                    <i class="material-icons">content_paste</i>
                    <p>Сверка</p>
                  </a>
              </li>
            <?php } ?>
          
          
            <?php  if(Yii::$app->user->can("client/index")){ ?>
                <li class="nav-item  <?php echo $active_route == 'spender/index' ? 'active' : '';?>">
                    <a class="nav-link" href="<?php echo Url::to(['/spender/index']);?>">
                      <i class="material-icons">notifications</i>
                      <p>Рассылка</p>
                    </a>
                </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("sender")){ ?>
                <li class="nav-item  <?php echo $active_route == 'sender/index' ? 'active' : '';?>">
                    <a class="nav-link" href="<?php echo Url::to(['/sender/index']);?>">
                      <i class="material-icons">person</i>
                      <p>Отправители</p>
                    </a>
                </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("transferspackage")){ ?>
                <li class="nav-item  <?php echo $active_route == 'transferspackage/index' ? 'active' : '';?>">
                    <a class="nav-link" href="<?php echo Url::to(['/transferspackage/index']);?>">
                      <i class="material-icons">content_paste</i>
                      <p>Переводы</p>
                    </a>
                </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("sellers")){ ?>
                <li class="nav-item  <?php echo $active_route == 'sellers/index' ? 'active' : '';?>">
                    <a class="nav-link" href="<?php echo Url::to(['/sellers/index']);?>">
                      <i class="material-icons">person</i>
                      <p>Поставщики</p>
                    </a>
                </li>
            <?php } ?>
          <?php }elseif(Yii::$app->user->identity->role->name == "client"){ ?>
            <li class="nav-item  <?php echo $active_route == 'client/profile' ? 'active' : '';?>">
                <a class="nav-link" href="<?php echo Url::to(['/client/profile']);?>">
                  <i class="material-icons">person</i>
                  <p>Личный кабинет</p>
                </a>
            </li>

            <?php  if(Yii::$app->user->can("site/sverka")){ ?>
              <li class="nav-item <?php echo $active_route == 'sverka/index' ? 'active' : '';?>">
                  <a class="nav-link" href="<?php echo Url::to(['/sverka/index']);?>">
                    <i class="material-icons">content_paste</i>
                    <p>Сверка</p>
                  </a>
              </li>
            <?php } ?>

            <?php  if(Yii::$app->user->can("autotruck/create")){ ?>
              <li class="nav-item <?php echo $active_route == 'autotruck/form' ? 'active' : '';?>">
                  <a class="nav-link" href="<?php echo Url::to(['/autotruck/form']);?>">
                    <i class="material-icons">dashboard</i>
                    <p>Создать заявку</p>
                  </a>
              </li>
            <?php } ?>

          <?php } ?>
          
        </ul>
      </div>
    </div>