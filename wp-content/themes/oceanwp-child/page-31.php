<!-- 這是要客製預約頁面用的 -->
<?php
get_header();
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/css/datepicker.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/js/datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/js/i18n/datepicker.zh.min.js"></script>
<script src="/wp-content/themes/oceanwp-child/assets/js/booking_step_one.js"></script>

<!-- Bootstrap Modal -->
<div class="modal fade" id="simpleTipModal" tabindex="-1" role="dialog" aria-labelledby="simpleTipModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="simpleTipModalTitle">簡易小提示-標題</h5>
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">-->
<!--                    <span aria-hidden="true">&times;</span>-->
<!--                </button>-->
            </div>
            <div class="modal-body">
                簡易小提示-內容
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal-btn" data-dismiss="modal">請點我關閉</button>
            </div>
        </div>
    </div>
</div>

<div class="mainDiv">
    <div class="progress">
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART1 簡易注意事項</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART2 選擇露營車位置</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART3 選擇露營車內容</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART4 查看注意事項</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART5 填訂購人資訊</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART6 填使用者資訊</div>
        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0">PART7 訂單資訊</div>
    </div>
    <div class="primary-content">
        <div class="mapDiv01 col-12 col-xl-8 order-1 order-xl-0">
            <table>
                <tbody class="mapTable">
                <?php
                for($i=0;$i<10;$i++){
                    echo '<tr>';
                    for($j=0;$j<10;$j++){
                        echo '<td class="camping-position" data-pd-id="" data-position="'.$i.','.$j.'">'.$i.','.$j.'</td>';
                    }
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
            <!--        <img class="mapImage" src="https://hayaku.com.tw/images/map_all.jpg">-->
        </div>
        <div class="mapDiv02 col-12 col-xl-4">
            <div>
                <div>
                    <div>
                        <div>
                            <h5 class="mb-0">請先選擇起始日期及天數，再於地圖點選帳篷。<br></h5>
                            <h6 class="mb-0" style="color:red">若帳篷不可選擇代表已被預訂。</h6>
                            <form name="p1_form" id="p1_form" action="https://hayaku.com.tw/index.php/Orders/booking_p1_process" method="post">
                                <div class="form-group">
                                    <div class="form-col">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-col form-col--arrival-date form-col--over">
                                        <div class="form-control">
                                            <input type="text" id="start_date" name="start_date" autocomplete="off" data-language='zh'>
                                        </div>
                                    </div>
                                    <div class="form-col">
                                        <div class="form-control">
                                            <div>
                                                <select name="days" id="days">
                                                    <option value="1" selected>1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="select_cart">
                                <div>現在選擇的露營車</div><div class="show_select_cart"></div>
                                <input type="checkbox" name="is_taking_pet" value="1">是否有攜帶寵物
                            </form>
                            <div>
                                <a class="button wpmc-nav-button to-step-two-btn">下一步選擇露營車資訊</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
