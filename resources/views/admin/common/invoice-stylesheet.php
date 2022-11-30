<style>
/* heading */

h1 { font: bold 130% sans-serif; letter-spacing: 0.5em; text-align: center; text-transform: uppercase; -webkit-print-color-adjust: exact; }

/* table */

table { font-size: 80%; table-layout: fixed; width: 100%; }
table { border-collapse: separate; border-spacing: 2px; }
th, td { border-width: 1px; padding: 0.5em; position: relative; text-align: left;  }
th, td { border-radius: 0.25em; border-style: solid; }
th { background: #EEE; border-color: #BBB; -webkit-print-color-adjust: exact; }
td { border-color: #DDD; -webkit-print-color-adjust: exact; }



/* header */
header h1 { background: linear-gradient(to right, rgb(0, 201, 255), rgb(146, 254, 157)); border-radius: 0.25em; color: #FFF; margin: 0 0 1em; padding: 0.5em 0; }
header address { float: left; font-size: 90%; font-style: normal; line-height: 1.25; margin: 0 1em 1em 0; width:50%;}
header address p { margin: 0 0 0.25em;}
/*header span, header img { display: block; float: right;}*/
header span { margin: 0 0 1em 1em; max-height: 25%; max-width: 60%; position: relative; }
/*header img { max-height: 100%; max-width: 100%; }*/


/* article */

article:after { clear: both; content: ""; display: table; }
article address { float: left; font-size: 125%; font-weight: bold; }

/* table meta & balance */

table.meta, table.balance { float: right; width: 36% !important; }
table.meta:after, table.balance:after { clear: both; content: ""; display: table; }
table.meta2 { float: left; width: 50% !important; }
table.meta:after { clear: both; content: ""; display: table; }


/* table meta */

table.meta th { width: 40%; }
table.meta td { width: 60%; }

/* table items */

table.inventory { clear: both; width: 100%; }
table.inventory th { font-weight: bold; text-align: center; }

table.inventory td:nth-child(1) { width: 20%; }
table.inventory td:nth-child(2) { text-align: right; width: 12%; }
table.inventory td:nth-child(3) { text-align: right; width: 12%; }
table.inventory td:nth-child(4) { text-align: right; width: 12%; }
table.inventory td:nth-child(5) { text-align: right; width: 12%; }
table.inventory td:nth-child(6) { text-align: right; width: 12%; }
table.inventory td:nth-child(7) { text-align: right; width: 12%; }
table.inventory td:nth-child(8) { text-align: right; width: 12%; }

/* table balance */

table.balance th, table.balance td { width: 50%; }
table.balance td { text-align: right; }

.beta{
    border: transparent;
    text-align: center;
}
.beta2{
    border: none;
    text-align: right;
    font: 14px/1 'Open Sans', sans-serif;
}
.beta3{
    border: none;
    /*padding:10px;*/
}
.beta4{
    border: none;
    text-align: left;
}
.red{
    color: #fc2d42;
}
.alert-danger {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    color: #fff;
    border: 1px solid transparent;
    padding: 13px;
    padding-right: 100px;
    float: left;
    margin-left: 5px;
    border-radius: 5px;
    background-color: red; 
    -webkit-print-color-adjust: exact;
}
.alert-success {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    color: #fff;
    border: 1px solid transparent;
    padding: 13px;
    padding-right: 100px;
    float: left;
    margin-left: 5px;
    border-radius: 5px;
    background-color: green; 
    -webkit-print-color-adjust: exact;    
}
.alert-warning {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    color: #fff;
    border: 1px solid transparent;
    padding: 13px;
    padding-right: 100px;
    float: left;
    margin-left: 5px;
    border-radius: 5px;
    background-color: #f39c12; 
    -webkit-print-color-adjust: exact;
}
.alert-info {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    color: #fff;
    border: 1px solid transparent;
    padding: 13px;
    padding-right: 100px;
    float: left;
    margin-left: 5px;
    border-radius: 5px;
    background-color: #00c0ef;
    -webkit-print-color-adjust: exact;
}



</style>