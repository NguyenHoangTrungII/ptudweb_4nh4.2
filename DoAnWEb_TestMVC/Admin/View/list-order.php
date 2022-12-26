<?php
    // session_start();
    require_once("include/menu.php");
    require_once("include/session.php");
    require_once("include/top.php");
    require_once("../Model/ModelAll.php");
    require_once("../config/databse.php");
	  require_once("../config/site.php");
    require_once("../Model/Pagination.php");
    require_once("../Controller/Order/getall-order.php");
?>

<!-- get role -->
<?php

$pagination = new Pagination;


  if($_SESSION['SMC_login_admin_type']==1){
    $_SESSION['SMC_login_admin_role']="Admin";
  }
  else {
    $_SESSION['SMC_login_admin_role']="Quản lý";
  }


  $config = array(
    'current_page'  => isset($_GET['page']) ? $_GET['page'] : 1,
    'total_record'  => count_all_member(), 
    'limit'         => 5,
    'link_full'     => 'list-order.php?page={page}',
    'link_first'    => 'list-order.php',
    'range'         => 3
  );


$pagination->init($config);

// Lấy limit, start
$limit = $pagination->get_config('limit');
$start = $pagination->get_config('start');

// var_dump($limit);


// Lấy danh sách thành viên
$orderList = getAll($limit, $start);

// var_dump($productList);

?>

<?php
  ?>

  <?php 

    if(!$ctrl->checkprivilege( $privilegeUser_array, "edit_order.php?id=4", $role)){
      $edit_order_status = "hidden";
    }else{
      $edit_order_status  = "";
    }
  
    if(!$ctrl->checkprivilege( $privilegeUser_array, "delete_order.php", $role)){
      $delete_order_status = "hidden";
    }else{
      $delete_order_status = "";
    }
  
    if($edit_order_status == "" || $delete_order_status == ""){
      $dow_status ="";
    }
    else{
      $dow_status ="hidden";
    }
  
  
  ?>

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Đơn hàng </span></h4>

            <hr class="my-5" />

            <div class="alert alert-danger alert-dismissible" role="alert" hidden>
              This is a danger dismissible alert — check it out!
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="alert alert-info alert-dismissible" role="alert" hidden >
              This is an info dismissible alert — check it out!
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <!-- Bootstrap Table with Header - Footer -->
            <div class="card">
              <div class="card-header header table-user">
                <h5 class=" card-header">Thông tin đơn hàng</h5>
              </div>
              <div class="table-responsive text-nowrap">
                <table class="table user">
                  <thead>
                    <tr>
                      <th class="id-header order">Mã</th>
                      <th class="name-customer order">Khách hàng</th>
                      <th class="address order">Đại chỉ</th>
                      <th class="total order">Tổng tiền</th>
                      <th class="ship-code order">Mã vận chuyện</th>
                      <th class="status order">Trạng thái</th>
                      <!-- <th class="payment order">Thanh toán</th> -->
                      <th class="action order" <?= $dow_status ?>>Thao tác</th>

                    </tr>
                  </thead>
                  <tbody>
                  <?php 

                      foreach($orderList AS $eachRow)
                      {
                        switch((int)$eachRow['trangthai']){
                          case 1: 
                            {
                              $status = '<option>Trạng thái</option> <option selected value="1">Chưa xác nhận</option> 
                                          <option value="2">Xác nhận</option>
                                          <option value="3">Đang vận chuyên</option>
                                          <option value="4">Đã giao</option>
                                          <option value="5">Đã hủy</option>';
                            }
                            break;
                          case 2: 
                            {
                              $status = '<option>Trạng thái</option> <option  value="1">Chưa xác nhận</option> 
                                          <option selected value="2">Xác nhận</option>
                                          <option value="3">Đang vận chuyên</option>
                                          <option value="4">Đã giao</option>
                                          <option value="5">Đã hủy</option>';
                            }
                            break;
                          case 3: 
                            {
                              $status = '<option>Trạng thái</option> <option value="1">Chưa xác nhận</option> 
                                          <option value="2">Xác nhận</option>
                                          <option selected value="3">Đang vận chuyên</option>
                                          <option value="4">Đã giao</option>
                                          <option value="5">Đã hủy</option>';
                            }
                            break;
                          case 4: 
                            {
                              $status = '<option>Trạng thái</option> <option value="1">Chưa xác nhận</option> 
                                          <option value="2">Xác nhận</option>
                                          <option value="3">Đang vận chuyên</option>
                                          <option selected value="4">Đã giao</option>
                                          <option value="5">Đã hủy</option>';
                            }
                            break;
                          case 5: 
                            {
                              $status = '<option>Trạng thái</option> <option  value="1">Chưa xác nhận</option> 
                                          <option value="2">Xác nhận</option>
                                          <option value="3">Đang vận chuyên</option>
                                          <option value="4">Đã giao</option>
                                          <option selected value="5">Đã hủy</option>';
                            }
                            break;

                        }
                          echo '
                          <tr>
                            <td id="'.$eachRow['id'].'" class="id-header user">
                            '.$eachRow['id'].'
                            </td>
                            <td class="name user">
                              '.$eachRow['hoten'].'
                            </td>
                            <td class="address order">
                            '.$eachRow['diachi'].'
                            </td>
                            <td class="total order">
                              <span>'.$eachRow['tongtienhang'].'</span>
                            </td>
                            <td class="ship-code order">
                              '.$eachRow['mavanchuyen'].'
                            </td>
                            <td class="status order">
                                <div>
                                  <select onchange="changeStatusOrder(this)" id="defaultSelect" class="form-select">
                                    '.$status.'
                                  </select>
                                </div>
                              </td>
                            
                            
                            <td '.$dow_status.'>
                              <div class="dropdown" '.$dow_status.'>
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                  <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">

                                  <a type="submit" class="dropdown-item" href="edit_order.php?id='.$eachRow['id'].'" '.$edit_order_status.'><i class="bx bx-edit-alt me-1"></i>
                                    Sửa</a>
                                  <p class="btn-delete dropdown-item" data-bs-toggle="modal" data-bs-target="#modalCenter" id='.$eachRow['id'].' href="" '.$delete_order_status.'><i class="bx bx-trash me-1" ></i> Xóa</p>
                                </div>
                              </div>
                            </td>
                          </tr>
                          ';
                          
}
                        ?>  
                    
                  </tbody>
                  <tfoot class="table-border-bottom-0">
                    <tr>
                      <th class="id-header order">Mã</th>
                      <th class="name-customer order">Khách hàng</th>
                      <th class="address order">Đại chỉ</th>
                      <th class="total order">Tổng tiền</th>
                      <th class="ship-code order">Mã vận chuyện</th>
                      <th class="status order">Trạng thái</th>
                      <!-- <th class="payment order">Thanh toán</th> -->
                      <th class="action order" <?= $dow_status ?>>Thao tác</th>
                    </tr>
                  </tfoot>


                </table>
                <div class="paging d-flex justify-content-end px-5 py-4">
                  <?php echo $pagination->html(); ?>
                </div>
              </div>
            </div>
          </div>
          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>


<?php
  include("include/tail.php");
?>




<script type="text/javascript">
$(".btn-delete").click(function(e){
    var del_id = $(this).attr('id');
        var $ele = $(this);
        Swal.fire({
        title: 'Bạn có muốn xóa đơn hàng này?',
        text: "Toàn bộ thông tin sẽ biến mất",
        icon: 'Cảnh báo',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: "http://localhost/DoAnWeb/DoAnWEb_TestMVC/admin/Controller/Order/delete-order.php",
              type:"POST",
              data:{del_id: del_id},
              
              success: function(response){
                if(response !=0){
                      console.log(response);
                      Swal.fire(
                      'Đã xóa!',
                      'Đơn hàng có ID '+del_id+' đã bị xóa',
                      'sucess')
                      $ele.parent().parent().parent().parent().slideToggle('slow'); 
                  } else{
                      Swal.fire(
                      'Thất bại',
                      'Đã xảy ra lỗi! Vui lòng thử lại',
                      'error'
                    )
              }
              }
             
                
 
          });
            
          }
        })
 });
</script>

<!-- real time  -->
<script>
  function  changeStatusOrder(e){
    var statusChoosen = $(e).find(":selected").val();
    var id_order = $(e).parent().parent().parent().find('td').html();


    $.ajax({
    url: "http://localhost/DoAnWeb/DoAnWeb_testMVC/admin/Controller/Order/checkrt-order.php",
    data: {id_order: id_order, statusChoosen: statusChoosen},
    type: 'POST',
    dataType: "text",
    success: function(data){ 
      if(data==1){
        $('.alert.alert-info.alert-dismissible').text("Sửa thông tin thành công");
        $('.alert.alert-danger.alert-dismissible').prop('hidden', true);
        $('.alert.alert-info.alert-dismissible').prop('hidden', false);
        $("html, body").animate({scrollTop: 0}, 1000);

        setTimeout(function(){
          $('.alert.alert-danger.alert-dismissible').prop('hidden', true);
          }, 2000);

      }
      else{
        $('.alert.alert-danger.alert-dismissible').text("Đã có lỗi xảy ra !! vui lòng thử lại sau");
        $('.alert.alert-info.alert-dismissible').prop('hidden', true);
        $('.alert.alert-danger.alert-dismissible').prop('hidden', false);
        $("html, body").animate({scrollTop: 0}, 1000);

        setTimeout(function(){
          $('.alert.alert-info.alert-dismissible').prop('hidden', true);
        }, 2000);
      }
    }
        
        
  });
};
</script>
