
<!-- NOT USE DUE TO RESPONSIVENESS PROBLEM -->

<!-- <style>
    .menu{
        height: 100%;
        width: 100%;
        position: relative;
        border: solid 2px red;

    }
    
    .sidebar{
        height: 100%;
        width: 60px;
        background: aliceblue;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        overflow: hidden;
        transition: all 0.5s ease;
        border: solid 2px red;
    }
    
    .mainHead{
        margin-left: 15px;
    }
    
    img{
        height: 40px;
        width: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .items{
        display: flex;
        align-items: center;
        font-size: 1.3rem;
        color: #000000CC;
        margin-left: 0px;
        padding: 10px 0px;
    }
    
    .sidebar li{
        margin-left: 10px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
        
    }
    
    .items i{
        margin: 0 10px;
    }
    
    .para{
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .sidebar li:not(.logout-btn):hover {
        background: #000;
        color: aliceblue;
    }
    
    .logout-btn{
        margin-top: 50px;
        color: #B70202;
    }
    
    .logout-btn:hover{
        background-color: #B70202;
        color: aliceblue;
    }
    
    .toggler{
        position: absolute;
        top: 0;
        left: 0px;
        padding: 10px 1px;
        font-size: 1.4rem;
        transition: all 0.5s ease;
    }
    
    .toggler #toggle-cross {
        display: none;
    }
    
    
    
    .active.toggler #toggle-cross {
        display: block;
    }
    
    .active.toggler #toggle-bars {
        display: none;
    }
    
    .active.toggler {
        left: 170px;
    }
    
    .active.sidebar {
        width: 220px;
    }
    
    .active.sidebar .para{
        opacity: 1;
    }
    a {
        text-decoration: none;
        color: inherit;
    }
    
    
</style>

<div class="menu">
        <div class="sidebar">
            <div class="logo items">
               
                <span class="mainHead para">
                    <h5>Hidalgo's</h5>
                    <h4>Apartment</h4>
                </span>
            </div>

            <li class="items">
                <i class="fa-solid fa-chart-simple"></i>
                <p class="para">Dashboard</p>
            </li>

            <li class="items">
                <i class="fa-solid fa-home"></i>
                <p class="para">Units</p>
            </li>
            <li class="items">
                <i class="fa-solid fa-user"></i>
                <p class="para">Tenants</p>
            </li>
            <li class="items">
                <i class="fa-solid fa-envelope"></i>
                <p class="para">Message</p>
            </li>

            <li class="items logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i>
                <p class="para"><a href="logout.php">Log-out</a></p>
            </li>
        </div>


        <div class="toggler">
                <i id="toggle-bars">                <img src="../assets/images/logov3.png" alt="">
                </i>
                <i class="fa-solid fa-xmark" id="toggle-cross"></i>
            </div>
      

    </div>

    <script>

const toggler = document.querySelector('.toggler')
const sidebar = document.querySelector('.sidebar')

const showFull = () => {
    toggler.addEventListener('click', ()=> {
        toggler.classList.toggle('active')
        sidebar.classList.toggle('active')
    })
}

showFull()
</script> -->
