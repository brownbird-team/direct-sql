<div class="pages-container">
    <div class="info">
        <h1>Pages</h1>
        <button>Add new</button>
    </div>
    <div class="pages-options">
        <div class="option">
            <h2>Home</h2>
            <h2>view</h2>
            <div class="opener">
                <div class="line1"></div>
                <div class="line2"></div>
            </div>
        </div>            
        <div class="pages-data">
            <div class="inputs">
                <p>New name for your page: </p>
                <input type="text" placeholder="New name:">
            </div>
            <div class="inputs">
                <p>Public</p>
                <label class="toggle" for="myToggle">
                    <input class="toggle__input" type="checkbox" id="myToggle">
                    <div class="toggle__fill"></div>
                </label>
            </div>
            <div class="inputs">
                <p>Url of your page: </p>
                <input type="text" placeholder="Url: ">
            </div>                
            <div class="buttons">
                <button>Save</button>
                <button>Edit</button>
                <button>Delete</button>
            </div>
        </div>            
    </div>        
</div>
<script>
    const opener = document.querySelector('.opener');
    const pages_data = document.querySelector('.pages-data');

    opener.addEventListener('click', () => {
        opener.classList.toggle('active');
        pages_data.classList.toggle('active');
    });
</script>