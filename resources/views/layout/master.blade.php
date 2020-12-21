<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Vet Clinic | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset('bower_components/jvectormap/jquery-jvectormap.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('bower_components/datatables/css/dataTables.bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/AdminLTE.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.css') }}">

  <!-- Vue JS -->
  <script src="{{ asset('vuejs/vue.js') }}"></script>

  <!-- Axios  -->
	<script src="{{ asset('vuejs/axios.js') }}"></script>

	<link rel="stylesheet" type='text/css' href="{{ asset('main/css/input-custom.css') }}">
	<link rel="stylesheet" type='text/css' href="{{ asset('main/css/global.css') }}">

  <!-- Google Font -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper" id="master-app">
    <input ref="baseUrl" type="hidden" value="{{ url('/') }}"/>
		<header class="main-header">
			<!-- Logo -->
			<a href="{{ url('/') }}" class="logo">
				<!-- mini logo for sidebar mini 50x50 pixels -->
				<span class="logo-mini"><b>B</b>VC</span>
				<!-- logo for regular state and mobile devices -->
				<span class="logo-lg"><b>BINTANG</b> <b>VET</b> CLINIC</span>
			</a>

			<!-- Header Navbar: style can be found in header.less -->
			<nav class="navbar navbar-static-top">
				<!-- Sidebar toggle button-->
				<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
					<span class="sr-only">Toggle navigation</span>
				</a>
				<!-- Navbar Right Menu -->
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<!-- User Account: style can be found in dropdown.less -->
						<li class="dropdown user user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image" alt="User Image">
								<span class="hidden-xs">Adiyansyah</span>
							</a>
							<ul class="dropdown-menu">
								<!-- User image -->
								<li class="user-header">
									<img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image">
									<p>
										Adiyansyah Dwi Putra - Dokter
									</p>
								</li>
								<!-- Menu Body -->
								{{-- <li class="user-body">
									<div class="row">
										<div class="col-xs-4 text-center">
											<a href="#">Followers</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Sales</a>
										</div>
										<div class="col-xs-4 text-center">
											<a href="#">Friends</a>
										</div>
									</div>
									<!-- /.row -->
								</li> --}}
								<!-- Menu Footer-->
								<li class="user-footer">
									<div class="pull-left">
										<a href="#" class="btn btn-default btn-flat">Profil</a>
									</div>
									<div class="pull-right">
										<a href="#" class="btn btn-default btn-flat" @click="onSignOut">Keluar</a>
									</div>
								</li>
							</ul>
						</li>
						<!-- Control Sidebar Toggle Button -->
						{{-- <li>
							<a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
						</li> --}}
					</ul>
				</div>

			</nav>
		</header>

		@component('layout.sidebar') @endcomponent

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>
					Sistem Administrasi Bintang Vet Clinic
				</h1>
				{{-- <ol class="breadcrumb">
					<li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
					<li class="active">Dashboard</li>
				</ol> --}}
			</section>

			<!-- Main content -->
			<section class="content">
				@yield('content')
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<footer class="main-footer">
			<div class="pull-right hidden-xs">
				<b>Version</b> 1.0
			</div>
			<strong>Copyright &copy; 2020 <a href="#">Vet Clinic</a>.</strong> All rights
			reserved.
		</footer>

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Create the tabs -->
			<ul class="nav nav-tabs nav-justified control-sidebar-tabs"></ul>
			<!-- Tab panes -->
			<div class="tab-content">
					<!-- Theme select tab content -->
					<div class="tab-pane" id="control-sidebar-home-tab">  </div>
			</div>
		</aside>
		<!-- /.control-sidebar -->
		<!-- Add the sidebar's background. This div must be placed
				immediately after the control sidebar -->
		<div class="control-sidebar-bg"></div>

	</div>
  <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('bootstrap/dist/js/bootstrap.min.js') }}"></script>

    @yield('vue-content')

    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- Sparkline -->
    <script src="{{ asset('bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
    <!-- jvectormap  -->
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('bower_components/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('bower_components/chart.js/Chart.js') }}"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('dist/js/pages/dashboard2.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    @yield('script-content')
    <script src="{{ asset('main/js/master-vue.js') }}"></script>
</body>
</html>
