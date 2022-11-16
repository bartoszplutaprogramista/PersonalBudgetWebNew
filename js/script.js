var date = new Date();

var day = date.getDate();
var month = date.getMonth() + 1;
var year = date.getFullYear();

if (month < 10) month = "0" + month;
if (day < 10) day = "0" + day;

var today = year + "-" + month + "-" + day;
document.getElementById('theDate').value = today;
document.getElementById('theDate').setAttribute("max", today);

function onlyNumberKey(evt) {
			var ASCIICode = (evt.which) ? evt.which : evt.keyCode
            if ((ASCIICode > 31 && ASCIICode < 44) || (ASCIICode > 44 && ASCIICode < 48)  || (ASCIICode > 57))
                return false;
            return true;
}
