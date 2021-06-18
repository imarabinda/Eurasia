@extends('template.index')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="page-title-box">
         <h4 class="page-title font-16"> {{$fabric->fabric_type()->value('name')}} - {{$fabric->fabric_color()->value('name')}}</h4>
         @can('fabric-edit')
         <a href="{{route('fabrics.edit', $fabric->id)}}" class="btn btn-info btn-action"><i class="dripicons-edit"></i> Edit Fabric</a>
         @endcan
      </div>
   </div>
</div>
<form id="fabric-form">
   <div class="row">
      <div class="col-lg-6">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row form-material">
                  <div class="col-md-5">
                     <div class="form-group">
                        <label>Receiving date*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$fabric->receiving_date}}">
                           <div class="input-group-append bg-custom b-0"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span></div>
                        </div>
                        <!-- input-group -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Mill ID*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$fabric->mill_id}}">
                        </div>
                        <!-- input-group -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Mill Ref ID*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$fabric->mill_ref_id}}">
                        </div>
                        <!-- input-group -->
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Fabric Type*</label>
                        <input disabled class="form-control" value="{{$fabric->fabric_type->name}}">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Fabric Color*</label>
                        <input disabled class="form-control" value="{{$fabric->fabric_color->name}}">
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Width*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$fabric->width}}">
                        </div>
                        <!-- input-group -->
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>Total quantity*</label>
                        <div class="input-group">
                           <input disabled class="form-control" value="{{$fabric->total_quantity}}">
                        </div>
                        <!-- input-group -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      
   <div class="col-lg-6">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row form-material">
               <div class="col-md-6">
                  <div class="form-group" style="margin-bottom:0!important">
                     <label>Enter each roll quantity*</label>
                     </div>
               </div>
               @can('roll-manage')
               <div class="col-md-2 align-self-end">
                  <div class="form-group" style="margin-bottom:0!important">
                        <a class="btn btn-secondary" target="_blank" type="button"  href="{{route('rolls.index', $fabric->id)}}">{{trans('Edit')}}</strong> </a>
                     </div>
                  </div>
               @endcan
               <style>
                  thead>tr{
                  background: #e2e2e2!important
                  }
               </style>
               <div class="col-md-12 table-responsive">
                  <p class="italic" style="color:red" id="roll_message"></p>
                  <table id="roll-table" class="table table-hover roll-list">
                     <thead>
                        <tr>
                           <th><i class="dripicons-view-apps"></i></th>
                           <th>{{trans('Roll ID')}}</th>
                           <th>{{trans('Roll Quantity')}}</th>
                        </tr>
                     </thead>
                     <tbody>
                        @foreach($fabric->fabric_rolls()->get() as $id => $roll)
                        <tr class="roll-row">
                           <td style="cursor:grab"><i class="dripicons-view-apps"></i></td>
                           <td>
                              <input disabled class="form-control" value="{{$roll->name}}" />
                           </td>
                           <td>
                              <input disabled class="form-control" value="{{$roll->quantity}}" />
                           </td>
                        </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>
@endsection