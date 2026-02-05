<?php
require_once __DIR__ . '/config/db.php';
include 'includes/header.php';

$sql = "SELECT id, code, name, sale_price, stock 
        FROM products 
        WHERE is_active = 1";

$res = $conn->query($sql);

if(!$res){
    die("Query Error: ".$conn->error);
}
?>

<h3>POS System</h3>

<table class="table table-bordered">
<thead>
<tr>
<th>Code</th>
<th>Name</th>
<th>Price</th>
<th>Stock</th>
<th></th>
</tr>
</thead>
<tbody>

<?php
while($row = $res->fetch_assoc()){
echo "<tr>
<td>{$row['code']}</td>
<td>{$row['name']}</td>
<td>{$row['sale_price']}</td>
<td>{$row['stock']}</td>
<td>
<button onclick='addToCart({$row['id']},\"{$row['name']}\",{$row['sale_price']})'>
Add
</button>
</td>
</tr>";
}
?>

</tbody>
</table>

<hr>

<h4>Cart</h4>

<table class="table table-bordered">
<thead>
<tr>
<th>Name</th>
<th>Price</th>
<th>Qty</th>
<th>Discount</th>
<th>Total</th>
<th></th>
</tr>
</thead>
<tbody id="cartBody"></tbody>
</table>

<h4>Subtotal: <span id="subtotal">0.00</span></h4>

<label>Extra Discount:</label>
<input type="number" id="extra_discount" value="0" onchange="renderCart()">

<br><br>

<h4>Grand Total: <span id="grand_total">0.00</span></h4>

<label>Paid Amount:</label>
<input type="number" id="paid_amount" value="0" onchange="calculateChange()">

<h4>Change: <span id="change_amount">0.00</span></h4>

<br>

<button onclick="saveSale()" class="btn btn-success">
Complete Sale
</button>

<script>
let cart = [];

function addToCart(id,name,price){

    let existing = cart.find(p => p.id === id);

    if(existing){
        existing.qty += 1;
    }else{
        cart.push({
            id:id,
            name:name,
            price:parseFloat(price),
            qty:1,
            discount:0
        });
    }

    renderCart();
}

function renderCart(){

    let html = '';
    let subtotal = 0;

    cart.forEach((item,index)=>{

        let rowTotal = (item.price * item.qty) - item.discount;
        subtotal += rowTotal;

        html += `
        <tr>
            <td>${item.name}</td>
            <td>${item.price}</td>
            <td>
                <input type="number" value="${item.qty}" min="1"
                onchange="updateQty(${index}, this.value)">
            </td>
            <td>
                <input type="number" value="${item.discount}"
                onchange="updateDiscount(${index}, this.value)">
            </td>
            <td>${rowTotal.toFixed(2)}</td>
            <td><button onclick="removeItem(${index})">X</button></td>
        </tr>`;
    });

    document.getElementById("cartBody").innerHTML = html;
    document.getElementById("subtotal").innerText = subtotal.toFixed(2);

    let extra = parseFloat(document.getElementById("extra_discount").value) || 0;
    let grand = subtotal - extra;

    document.getElementById("grand_total").innerText = grand.toFixed(2);

    calculateChange();
}

function updateQty(index,val){
    cart[index].qty = parseInt(val);
    renderCart();
}

function updateDiscount(index,val){
    cart[index].discount = parseFloat(val) || 0;
    renderCart();
}

function removeItem(index){
    cart.splice(index,1);
    renderCart();
}

function calculateChange(){
    let paid = parseFloat(document.getElementById("paid_amount").value) || 0;
    let grand = parseFloat(document.getElementById("grand_total").innerText) || 0;

    let change = paid - grand;
    document.getElementById("change_amount").innerText = change.toFixed(2);
}

function saveSale(){

    if(cart.length === 0){
        alert("Cart Empty");
        return;
    }

    let data = {
        cart: cart,
        extra_discount: document.getElementById("extra_discount").value,
        paid_amount: document.getElementById("paid_amount").value
    };

    fetch("save_sale.php",{
        method:"POST",
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify(data)
    })
    .then(res=>res.json())
    .then(response=>{
        if(response.status==="success"){
            window.location.href="view_invoice.php?id="+response.sale_id;
        }else{
            alert("Error Saving Sale");
            console.log(response);
        }
    })
    .catch(err=>{
        console.log(err);
        alert("Server Error");
    });
}
</script>

<?php include 'includes/footer.php'; ?>
