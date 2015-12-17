<?php echo $header; ?>

<?php echo $column_left; ?>

<div id="content">

    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="panel panel-default">
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#projekod" data-toggle="tab"><i class="fa fa-info-circle"></i> Bilgilendirme</a></li>
                        <li><a href="#template_edit" data-toggle="tab"><i class="fa fa-file-image-o"></i> Template Ayarları</a></li>
                        <li><a href="#sms_queue" data-toggle="tab"><i class="fa fa-list"></i> Sms Kuyruğu</a></li>
                        <li><a href="#sms_send" data-toggle="tab"><i class="fa fa-send"></i> Toplu Sms Gönder</a></li>
                        <li><a href="#tab-aramalar" data-toggle="tab"><i class="fa fa-phone-square"></i> Arama Geçmişi</a></li>
                        <li><a href="#ayarlar" data-toggle="tab"><i class="fa fa-gear"></i> Ayarlar</a></li>
                    </ul>
                </form>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="projekod">
                    <div style="margin:20px;margin-top:5px;">
                        <fieldset class="text-center">
                            <img src="<?=$url;?>/admin/view/image/bulutfonlogo.png" alt="bulutfon logo"/>
                            <legend>Bulutfon</legend>
                            <p><strong><a href="http://bulutfon.com" target="_blank">Bulutfon</a> </strong> Bir kaç adımda, kurulum gerektirmeden anında akıllı ofis
                            telefon sistemi kurabileceğiniz online bir hizmettir.</p>
                            <p>
                                Bu eklenti ile kullandığınız Opencart üzerinden bulutfon özelliklerini yönetebilir ve raporlama yapabilirsiniz.
                            </p>
                        </fieldset>
                        <div class="ara" style="display: block;height: 45px;"></div>
                        <fieldset class="text-center">
                            <legend>Projekod</legend>
                            <img src="<?=$url;?>/admin/view/image/projekodlogo.png"  alt="projekod logo" />
                            <p>
                                <a href="http://projekod.com" target="_blank">Projekod Yazılım</a> 2013 yılında Denizli’de kurulan, masaüstü ve Web tabanlı intranet yazılımlar üreten, bunun yanında bir çok projeye outsource destek veren bir firmadır.
                            </p>
                            <p>
                                Kullanmakta olduğunuz yazılım <a href="http://projekod.com" target="_blank">Projekod Yazılım</a> tarafından geliştirilmiştir.
                            </p>
                        </fieldset>
                    </div>
                </div>
                <div class="tab-pane" id="tab-aramalar">
                    <table class="table table-cencored">
                        <thead>
                            <tr>
                                <th>Arama Tipi</th>
                                <th>Yön</th>
                                <th>Arayan</th>
                                <th>Aranan</th>
                                <th>Arama Zamanı</th>
                                <th>Cevaplama Zamanı</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cdrs as $cdr): ?>
                                <tr>
                                    <td><?=$cdr["bf_calltype_str"];?></td>
                                    <td><?=$cdr["direction_str"];?></td>
                                    <td>

                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary"><?=$cdr["caller_str"];?></button>
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#">Action</a></li>
                                                <li><a href="#">Another action</a></li>
                                                <li><a href="#">Something else here</a></li>
                                                <li role="separator" class="divider"></li>
                                                <li><a href="#">Separated link</a></li>
                                            </ul>
                                        </div>

                                    </td>
                                    <td><?=$cdr["callee_str"];?></td>
                                    <td><?=$cdr["call_time"];?></td>
                                    <td><?=$cdr["answer_time"];?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


                <div class="tab-pane" id="ayarlar">
                    <div style="margin:20px;margin-top:5px;">
                        <form action="" method="post" name="settingForm">
                            <fieldset>
                                <legend>Api Ayarları</legend>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-name">Master Key [*]</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bulutfon_masterKey" value="<?=$ayar_masterKey;?>" placeholder="Master Key"  class="form-control" />
                                    </div>
                                </div>
                                <br/>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-name">Sms Başlığı</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bulutfon_sms_baslik" value="<?=$ayar_sms_baslik;?>" placeholder="Sms Başlığı"  class="form-control" />
                                    </div>
                                </div>
                                <br/>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-name">Santral Numaraları</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bulutfon_sms_numaralar" value="<?=$ayar_sms_numaralar;?>" placeholder="90XXXXXXXXXX,90XXXXXXXXXX"  class="form-control" />
                                    </div>
                                </div>
                            </fieldset>
                            <br/>
                            <br/>
                            <br/>
                            <fieldset>
                                <legend>Sms İle Bildirim Ayarları</legend>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label><input type="checkbox" name="bulutfon_notify_onOrderComplete" value="1"  <?=($ayar_notify_onOrderComplete) ? 'checked' : '';?>/> Sipariş tamamlandığında sms gönder</label><br/>
                                        <label><input type="checkbox" name="bulutfon_notify_onOrderStatusChange" value="1"  <?=($ayar_notify_onOrderStatusChange) ? 'checked' : '';?>/> Sipariş durumu değiştiğinde sms gönder</label><br/>
                                        <label><input type="checkbox" name="bulutfon_notify_onNewUser" value="1"  <?=($ayar_notify_onNewUser) ? 'checked' : '';?>/> Yeni üye olunduğunda sms gönder</label><br/>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="clearfix"></div>
                            <br/>
                            <br/>
                            <br/>
                            <br/>
                            <fieldset>
                                <legend>Sms Gönderme Bilgileri</legend>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-name">Tek Seferde İşlenecek Adet</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bulutfon_sms_cronCount" value="<?=$ayar_sms_cronCount;?>" placeholder="Sms Gönderim Adet"  class="form-control" />
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <br/>
                                <p>
                                    Bulutfon Opencart Eklentisi Smslerini önce kuyruğa alır ardından toplu olarak gönderim yapar. <br/>
                                    Aşağıdaki Cron dosyasını kullanarak sms gönderim kuyruğunu çalıştırabilirsiniz. Bu ayar her çalıştırma sırasında kuyruktan kaçar tane sms gönderileceğini
                                    belirtebileceğiniz ayardır.
                                </p>
                                <br/>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="well">
                                            <?=$url;?>/?route=module/bulutfon&action=cron&token=<?=$securityKey;?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="<?=$url;?>/?route=module/bulutfon&action=cron&token=<?=$securityKey;?>" target="_blank" class="btn btn-primary"><i class="fa fa-fast-forward"></i> Şimdi Çalıştır</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input-name">Güvenlik Kodu</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="bulutfon_secureKey" value="<?=$securityKey;?>" placeholder="Güvenlik Kodu"  class="form-control" />
                                    </div>
                                </div>
                                <br/>
                                <br/>
                                <p>
                                    Yeniden oluşturmak için boş bırakınız
                                </p>
                                <br/>
                                <p>
                                    Yukarıda yazılı adresi belli zaman aralıkları ile çalıştırsanız smsleriniz belirlediğiniz şekilde gönderilecektir.
                                </p>
                            </fieldset>
                            <br/>
                            <button type="submit" name="setting_update" class="btn btn-success"><i class="fa fa-check"></i> Ayarları Güncelle</button>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="template_edit">
                    <div style="margin:20px;">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <form method="post" action="<?php echo $action; ?>">
                                <?php foreach($default_sms_templates as $key => $template): ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingOne_<?=$key;?>">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne_<?=$key;?>" aria-expanded="true" aria-controls="collapseOne">
                                                <?php echo $template['name']; ?>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseOne_<?=$key;?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <textarea rows="3" class="form-control" name="templates[<?=$template['name'];?>]"><?php echo $template['content']; ?></textarea>
                                            <div class="well">
                                                Gönderiler içerisinde değişken kullanabilirsiniz,<br/>
                                                Sipariş ile ilgii olmayan Kişisel gönderilerde :<strong>{adi} {soyadi}</strong> <br/>
                                                Sipariş ile ilgili gönderilerde : <strong>{adi} {soyadi} {siparis_no} {siparis_durum}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach;?>
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="head_add_new_template">
                                        <h4 class="panel-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#col_add_new_template" aria-expanded="true" aria-controls="collapseOne">
                                                Yeni Sms Template Olustur
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="col_add_new_template" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="panel-body">
                                            <label class="form-group">
                                                <h4><b>Sms Ismini giriniz:</b></h4>
                                                <input type="text" class="form-control" name="sms_name">
                                            </label>
                                            <h4><b>Sms içeriğini giriniz:</b></h4>
                                            <textarea rows="3" class="form-control" name="sms_content"></textarea>
                                            <div class="well">
                                                Gönderiler içerisinde değişken kullanabilirsiniz,<br/>
                                                Bu gönderi ile berabar kullanabileceğiniz değişkenler <strong>{adi} {soyadi}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <button type="submit" class="btn btn-success" name="template_update"><i class="fa fa-check"></i> Güncelle</button>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="sms_queue">
                    <div style="margin:20px;">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>Numara</th>
                                <th>Mesaj İçeriği</th>
                                <th>Arguments</th>
                                <th>Template ID</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($sms_queue as $key => $queue): ?>
                            <?php if($queue['status'] == 1) { ?>
                            <tr class="info">
                                <?php } else { ?>
                            <tr class="success">
                                <?php } ?>
                                <th><?php echo $queue['date_added']?></th>
                                <th><?php echo $queue['phone_number']?></th>
                                <th><?php echo $queue['sms_content']?></th>
                                <th><?php echo $queue['arguments']?></th>
                                <th title="<?php echo $all_sms_template[$queue['template_id']-1]['content']; ?>">
                                    <a href="#template_edit" data-toggle="tab"><?php echo $queue['template_id']?></a>
                                </th>
                                <th>
                                    <?php if($queue['status']==1): ?>
                                        <label class="label label-primary">Bekliyor</label>
                                    <?php elseif($queue['status']==2): ?>
                                        <label class="label label-primary">Gönderildi</label>
                                    <?php endif; ?>
                                </th>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="sms_send">
                    <div style="margin:20px;">
                        <form class="form-group" method="post" action="<?php echo $action; ?>">
                            <div class="portlet-content">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th><center><input type="checkbox"></center></th>
                                            <th>Ad Soyad</th>
                                            <th>Mail Adresi</th>
                                            <th>GSM</th>
                                            <th>Kayıt Tarihi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($customers as $key => $customer): ?>
                                        <tr>
                                            <td><center><input type="checkbox" name="customerId[]" value="<?php echo $customer['customer_id']?>"></center></td>
                                            <td><?php echo $customer['firstname'].' '.$customer['lastname'];?></td>
                                            <td><?php echo $customer['email'];?></td>
                                            <td><?php echo $customer['telephone'];?></td>
                                            <td><?php echo $customer['date_added'];?></td>
                                        </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <label class="form-group">
                                <select class="selectpicker" data-style="btn-inverse" name="sms_id">
                                    <?php foreach($customer_sms_templates as $key => $template): ?>
                                    <option value="<?=$template['id'];?>"><?=$template['name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </label>
                            <br>
                            <label class="form-group">
                                <button type="submit" name="customers" class="btn btn-success"><i class="fa fa-send"></i> Gönder</button>
                            </label>
                        </form>
                    </div>
                </div>
            </div>
            <div>
            </div>

        </div>

        <?php echo $footer; ?>