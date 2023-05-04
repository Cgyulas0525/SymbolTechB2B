<style>

    .colored-toast.swal2-icon-success {
        background-color: #a5dc86 !important;
    }

    .colored-toast.swal2-icon-error {
        background-color: #f27474 !important;
    }

    .colored-toast.swal2-icon-warning {
        background-color: #f8bb86 !important;
    }

    .colored-toast.swal2-icon-info {
        background-color: #3fc3ee !important;
    }

    .colored-toast.swal2-icon-question {
        background-color: #87adbd !important;
    }

    .colored-toast .swal2-title {
        color: white;
    }

    .colored-toast .swal2-close {
        color: white;
    }

    .colored-toast .swal2-html-container {
        color: white;
    }

    .mylabel {
        margin-top: 7px;
    }

    .mylabel-left {
        margin-top: 7px;
        text-align: left;
    }

    .mylabel8{
        margin-top: 7px;
        font-size: 1em;
    }

    .cellLabel {
        font-size: 1em;
        height: 2.5em;
    }


    .vmi {
        display: block;
        width:100%;
        height:100vh;
        background-image: url('http://localhost/Laravel/SymbolB2B/public/img/B2B_3.jpg');
        background-repeat: no-repeat;
        background-size: cover;
    }
    tr.odd td:first-child,
    tr.even td:first-child {
        padding-left: 4em;
    }

    .adminDBButton {
        border: 2px solid gray;
        box-shadow: 0px 8px 15px rgba(0,0,0,0.1);
        font-size: 15px;
        margin-left: 5px;
        width: 90%;
    }

    .firstSUDataUploadButton {
        border: 2px solid gray;
        box-shadow: 0px 8px 15px rgba(0,0,0,0.1);
        font-size: 15px;
        margin-left: 5px;
        margin-top: 250px;
        margin-bottom: 250px;
        width: 90%;
    }

    .swCancelButton {
        background-repeat: no-repeat;
        background-size: 98px 84px;
        /*background-color: red;*/
        width: 100px;
        height: 86px;
        text-align: center;
        color: white;
        font-weight: bold;
        font-size:20px;
        font-family: 'Palatino, URW Palladio L, serif'

    }

    .swConfirmButton {
        background-repeat: no-repeat;
        background-size: 98px 84px;
        width: 100px;
        height: 86px;
        text-align: center;
        color: white;
        font-weight: bold;
        font-size:20px;
        font-family: 'Palatino, URW Palladio L, serif'

    }

    .hide-col {
        width: 0px !important;
        height: 0px !important;
        display: block !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    .font20px {
        font-size:20px;
    }

    .formalignright {
        text-align: right !important;
    }

    .leftmargin1em {
        margin-left: 1em;
    }

    .rigthmargin1em {
        margin-left: 1em;
    }

    .topmarginMinusz1em {
        margin-top: -1em;
    }

    .topmarginMinusz2em {
        margin-top: -2em;
    }

    .topmarginMinusz3em {
        margin-top: -3em;
    }

    .topmargin1em {
        margin-top: 1em;
    }

    .topmargin2em {
        margin-top: 2em;
    }

    .topmargin3em {
        margin-top: 3em;
    }

    .margintop10 {
        margin-top: 10px;
    }

    .picture-200 {
        width: 200px;
        height: 200px;
    }
    .picture-small {
        width: 2em;
        height: 2em;
    }
    .main-sidebar {
        background-color: #9EBE37 !important
    }

    .brand-text {
        color: #FFFFFF;
    }

    .main-header {
        background-color: #9EBE37 !important
    }

    .main-footer {
        background-color: #9EBE37 !important;
        color: #FFFFFF !important;
    }

    .nav-item {
        color: #FFFFFF !important;
    }

    .nav-link {
        color: #FFFFFF !important;
    }

    .navbar {
        background: #2C3E50;
    }

    .font-weight-600 {
        font-weight: 600 !important;
    }

    .sajatBox {
        background-color: lightseagreen !important;
        margin-top: 10px !important;
    }


    .welcomecenter {
        display: flex;
        /*background: url('public/img/B2B_3.jpg') center center;*/
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

     .center {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100px;
    }

    .hozamgomb {
        background-repeat: no-repeat;
        background-size: 98px 84px;
        width: 100px;
        height: 86px;
        text-align: center;
        color: red;
        text-shadow: 1px 1px #060503;
        font-weight: bold;
        font-size:20px;
        font-family: 'Palatino, URW Palladio L, serif'
    }

    .dashboardgomb {
        background-repeat: no-repeat;
        background-size: 168px 84px;
        width: 170px;
        height: 86px;
        text-align: center;
        color: red;
        text-shadow: 1px 1px #060503;
        font-weight: bold;
        font-size:25px;
        font-family: 'Palatino, URW Palladio L, serif'
    }

    .inputWrapper label{
      display:block;
    }

    div.dataTables_wrapper  div.dataTables_filter {
        width: 100%;
        float: none;
        text-align: right;
        margin-top: -2.2em;
    }

    div.dataTables_paginate {
        width: 100%;
        float: none;
        text-align: right;
    }

    .dataTables_filter {
        float: left !important;
        margin-bottom: 0.5em;
    }

    div.dataTables_length {
        margin-right: 1em;
    }

    div.dt-buttons {
        float: left;
        margin-left:10px;
        margin-right:10px;
    }

    /*.table {*/
    /*    table-layout: fixed;*/
    /*}*/


    .table thead th {
        width: auto;
        background-image: none !important;
        background-color: lightgrey !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-weight: bold !important;
        font-size: 16px !important;
        max-height: 1.1em !important;
    }

    .table tfoot th {
        width: auto;
        background-image: none !important;
        background-color: lightgrey !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-weight: bold !important;
    }


    .table tbody tr td {
        width: auto;
        background-image: none !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    .table.dataTable tbody tr.selected {
        color: black !important;
        background-color: gray !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-size: 14px !important;
        font-weight: bold !important;
    }

    .tfoot.input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

    .td.highlight {
        font-weight: bold;
        background-color: #3c8dbc;
        color: blue;
    }

    .buttons-copy {
        background-color: lightgrey;
        color: black;
    }

    .buttons-excel {
        background-color: lightgrey;
        color: black;
    }

    .buttons-pdf {
        background-color: lightgrey;
        color: black;
    }

    .buttons-csv {
        background-color: lightgrey;
        color: black;
    }


    .table.dataTable tbody th, .table.dataTable tbody td, .table.dataTable tbody tf {
        padding: 4px 4px; /* e.g. change 8x to 4px here */
    }

    .m-t-0 {
        text-align: center;
    }

    .content-dashboard {
        background: url('http://priestago.hu/tarca/public/img/brand/bolt.png') center center;
        background-repeat: no-repeat;
        background-size: cover;
        min-height: 800px;
    }

    .panel-footer{
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 25px;
        border-bottom-left-radius: 25px;
        border-top: 2px solid lightgray;
        padding: 10px;
        background-color: white;
        font-family: Palatino, URW Palladio L, serif !important;
        font-size: 15px !important;
        font-weight: bold !important;
     }

    .widget-user-header{
        height: 105px;
    }

    .imgcenter {
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
    }

    .image__file-upload {
        padding: 10px;
        background: #20a8d8;
        display: table;
        color: white;
        border-radius: 0.25rem;
        border-color: #20a8d8;
    }

    .image__file-upload .d-none {
        display: none;
    }

    .image__file-upload:hover {
        cursor: pointer;
        background-color: #1985ac;
        border-color: #187da0;
    }

    .mylabel {
        margin-top: 7px;
    }

    .mylabel8{
        margin-top: 7px;
        font-size: 1em;
    }

    .cellLabel {
        font-size: 1em;
        height: 2.5em;
    }

    .table.dataTable tr.group, tr.group:hover {
        background-color: #ddd !important;
    }

    .myCheckbox {
        text-align : center;
        border: 1px solid red; /* for illustration only */
    }

    .honaplezart {
        background-color: lightgray !important;
    }

    .alapbg {
        background-color: white !important;
    }

    .hetvege {
        background-color: lightgray;
    }

    .elteres {
        background-color: yellow;
    }

    .indexicon {
        width: 40px;
        height: 40px;
    }

    .indexicon25 {
        width: 25px;
        height: 25px;
    }

    .clearfix {
    .clearfix();
    }
    .center-block {
    .center-block();
    }
    .pull-right {
        float: right !important;
    }
    .pull-left {
        float: left !important;
    }

    .tablesmall thead th {
        width: auto;
        background-image: none !important;
        background-color: lightgrey !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-weight: bold !important;
        font-size: 16px !important;
        height: 20px !important;
    }

    .tablesmall tfoot th {
        width: auto;
        background-image: none !important;
        background-color: lightgrey !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-weight: bold !important;
    }


    .tablesmall tbody tr td {
        width: auto;
        background-image: none !important;
        color: black !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    .tablesmall.dataTable tbody tr.selected {
        color: black !important;
        background-color: gray !important;
        font-family: Palatino, URW Palladio L, serif !important;
        font-size: 14px !important;
        font-weight: bold !important;
    }


</style>
