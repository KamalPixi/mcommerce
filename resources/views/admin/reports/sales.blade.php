@extends('admin.app')
  @section('content')

  <!-- error message display -->
  @if($errors->any())
      @foreach($errors->all() as $error)
          @include('admin.parts.flash', $flash_msg = ['class' => 'alert-danger','title' => 'Error!','msg' => $error])
      @endforeach
  @endif

  @if(Session::has('success'))
    @include('admin.parts.flash', $flash_msg = ['class' => 'alert-success','title' => 'Success','msg' => Session::get('success')])
  @endif

  @if(Session::has('fail'))
    @include('admin.parts.flash', $flash_msg = ['class' => 'alert-danger','title' => 'Error!','msg' => Session::get('fail')])
  @endif
  <!-- end error msg -->


  <div class="wrapper">
    @include('admin.includes.nav')
    @include('admin.includes.aside', ['data'=>'some data'])

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    <!--breadcrumb-->
    @include('admin.includes.breadcrumb')

    <!-- Main content -->
    <div class="container-fluid">

      <!-- Info boxes -->
      <div class="row my-2">
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box">
              <span class="info-box-icon bg-info elevation-1"><i class="fas fa-cart-arrow-down"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">TOTAL ORDERS</span>
              <span class="info-box-number">
                  {{ $sales['total_orders'] ?? 0 }}
                  <small></small>
              </span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-luggage-cart"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">ITEM SOLD</span>
              <span class="info-box-number">{{ $sales['item_sold'] ?? 0 }}</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix hidden-md-up"></div>

          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">PRICE</span>
              <span class="info-box-number">{{ $sales['price'] ?? 0 }}</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">SHIPPED OUT</span>
              <span class="info-box-number">{{ $sales['shipped_out'] ?? 0 }}</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">PENDING</span>
              <span class="info-box-number">{{ $sales['pending'] ?? 0 }}</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">COMPLETED</span>
              <span class="info-box-number">{{ $sales['completed'] ?? 0 }}</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">---</span>
              <span class="info-box-number">-</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <!-- /.col -->
          <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
              <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>

              <div class="info-box-content">
              <span class="info-box-text">---</span>
              <span class="info-box-number">-</span>
              </div>
              <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          </div>
          <!-- /.col -->
      </div>
      <!-- /.row -->



      <div class="row">
      <div class="col-12">
      <div class="card">
          <div class="card-header">
          <h3 class="card-title">Sales Report</h3>

          <div class="card-tools">
              <div class="input-group input-group-sm" style="justify-content:right">
                <form class="d-flex align-items-center" action="@if(Route::has('admin.reports')) {{ route('reports.index') }} @endif" method="GET">
                  <p class="m-0 mr-1">Bydate:</p>
                  <input class="form-control mr-1" type="date" name="date" value="{{ Request::query('date') ?? '' }}">
                  <input class="btn btn-sm btn-info align-self-center mr-2" type="submit" name="" value="Show">
                </form>
                  <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm align-self-center">Back</a>
              </div>
          </div>
          </div>
          <!-- /.card-header -->

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Total Qty</th>
                    <th>Shipping</th>
                    <th>Shipping Cost</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($order_master))
                    @foreach($order_master as $key => $co)
                    <tr>
                        <td>{{ $co->getKey() ?? ''}}</td>
                        <td>{{ $co->user->last_name ?? '' }}</td>
                        <td>{{ $co->total_qty ?? '' }}</td>
                        <td>{{ $co->shippingMethod->name ?? '' }}</td>
                        <td>{{ $co->shippingMethod->cost ?? '' }}</td>
                        <td>{{ $co->total_price ?? '' }}</td>
                        <td>{{ $co->status ?? '' }}</td>

                        <td style="width:1.5rem;">
                            <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item" href="@if( Route::has('pending_orders.edit') ) {{ route('order_master.show', $co->id) }} @endif">View</a>

                                <form class="" action="@if( Route::has('pending_orders.destroy') ) {{ route('pending_orders.destroy', $co->id) }} @endif" method="POST">
                                  @CSRF
                                  @method('DELETE')
                                  <input class="dropdown-item" type="submit" name="destroy" value="Delete">
                                </form>
                                </div>
                            </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
          </div>
          <!-- /.card-body -->
      </div>
      <!-- /.card -->
      </div>
      </div>
      <!-- /.row -->

      <div class="d-flex justify-content-end">
        {{ $order_master->links() }}
      </div>


    </div>
    <!-- End Main content -->
    </div>
  <!-- /.content-wrapper -->
  @include('admin.includes.footer')
  <!-- /.control-sidebar -->
  @include('admin.includes.side_controll')
  </div>
  <!-- ./wrapper -->
  @endsection('content')
