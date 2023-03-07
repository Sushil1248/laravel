@extends('admin.layouts.app')
@section('title', '- Invoices')

@section('content')
<section class="order-listing Invoice-listing">
    <div class="container">
        <div class="left-content d-flex">
            <div class="list-title d-flex">
                <img src="{{ asset('assets/images/invoice.png') }}">
                <div class="list-content">
                    <h2 class="heading-text">Invoicing</h2>
                    <h2 class="mobile-text d-none">Invoices</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. A suspendisse diam tincidunt vel neque
                        vulputate massa.
                    </p>
                </div>
            </div>
            <div class="right-btns">
                <div class="dropdown custom-dropdown">
                    <a class="nav-link btn navy-blue-btn" href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false">Create Invoice
                        <img src="{{ asset('assets/images/dropdown.svg') }}">
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">PO Invoice</a>
                        <a class="dropdown-item" href="#">NPO Invoice</a>
                        <a class="dropdown-item" href="#">Credit Memo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="order-listing Invoice-tabs">
            <!-- tabs Start here -->
            <ul class="tabs nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tabs-1" role="tab">All Invoices</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-2" role="tab">Drafts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tabs-3" role="tab">In-Process</a>
                </li>
            </ul>
            <!-- Search section Start here -->
            <div class="list-header d-flex justify-content-between">
                <form class="form-inline my-2 my-lg-0">
                    <button class="btn btn-outline-dark my-2 my-sm-0 form-control-feedback" type="submit"><img src="{{ asset('assets/images/search-filter.svg') }}"></button>
                    <input class="form-control search-input" type="search" placeholder="Search Invoice number" aria-label="Search">
                </form>
                <div class="list-filters d-flex  align-items-center   ">
                    <ul class="d-flex justify-content-between align-items-center">
                        <li>
                            <h6>Applied Filters:</h6>
                        </li>
                        <li><button class="filter-text">Type <img src="{{ asset('assets/images/close.svg') }}"></button></li>
                        <li><button class="filter-text">Approved<img src="{{ asset('assets/images/close.svg') }}"></button></li>
                        <li class="filters-btn"><button class="filter-text" id="filterButton"> <img src="{{ asset('assets/images/Filters lines.svg') }}"> Filters</button></li>
                    </ul>
                </div>
            </div>
            <!-- Search section End here -->
            <div class="tab-content invoice-tab-content">
                <div class="tab-pane active" id="tabs-1" role="tabpanel">
                    <div class=" table-responsive list-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="purchase-order-date">Invoice #</th>
                                    <th scope="col">Purchase Order#</th>
                                    <th scope="col">Invoice Type</th>
                                    <th scope="col">Invoice Date</th>
                                    <th scope="col" class="created-title">Created Date <img src="{{ asset('assets/images/down-a.svg') }}"></th>
                                    <th scope="col" class="text-center">Amount</th>
                                    <th scope="col" class="text-center status-text">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="purchase-order-date">
                                        <div class="scope-item">
                                            <h5 class="invoice-desktop-text">9506932861</h5>
                                            <div class="mobile-text d-none">
                                                <h5>Inv-124567 <span class="tag-text"><img src="{{ asset('assets/images/tags.svg') }}" alt="Invoice tag">PO</span></h5>
                                                <p class="price-text">64,120.0 $</p>
                                                <span class="date-text">Jan 08, 2022</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="scope-item">
                                            <h5>41000679224</h5>
                                            <p>#75-598 - Rob’s - PLYM - 12-21-2020</p>
                                        </div>
                                    </td>
                                    <td>
                                        PO Fire/EMS/Towing/Hazmat
                                    </td>
                                    <td>Jan 08, 2022</td>
                                    <td>Jan 08, 2022</td>
                                    <td>
                                        <price>64,120.0 $</price>
                                    </td>
                                    <td class="text-center status-text"><span class="error-text open-text"><span class="dot"></span>In-Process</span></td>
                                    <td class="text-right">
                                        <button class="sidebar-popup" type="button"><img src="{{ asset('assets/images/new.svg') }}"></button>
                                    </td>
                                </tr>
                                
                            </tbody>
                            <tfoot class="table-footer">
                                <tr>
                                    <td colspan="12">
                                        <ul class="pagination">
                                            <li class="page-item">Rows per page:</li>
                                            <li class="page-item footer-drop">10 <img src="{{ asset('assets/images/drop-down.svg') }}"></li>
                                            <li class="page-item pages">1-10 of 24</li>
                                            <li class="page-item"><a class="page-link" href="#"><img src="{{ asset('assets/images/left-a.svg') }}"></a>
                                            </li>
                                            <li class="page-item"><a class="page-link" href="#"><img src="{{ asset('assets/images/right-a.svg') }}"></a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td  colspan="12" class="mobile-pagination">
                                        <div class="pagination-list">
                                            <span class="left-arrow"><img src="{{ asset('assets/images/arrow-left.svg') }}"></span>
                                            <p class="text-center">Page 1 of 10</p>
                                            <span class="right-arrow"><img src="{{ asset('assets/images/arrow-right.svg') }}"></span>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="tab-pane" id="tabs-2" role="tabpanel">
                    <p>Second Panel</p>
                </div>
                <div class="tab-pane" id="tabs-3" role="tabpanel">
                    <p>Third Panel</p>
                </div>
            </div>
            <!-- tabs End here -->
        </div>
    </div>
</section>
@parent
@endsection
                                    