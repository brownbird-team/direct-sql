<div class="pages-container">
    <div class="info">
        <h1>Files</h1>
        <button>Add file</button>
    </div>
    <div class="pages-options">
        <div class="options">
            <h2>Home</h2>
            <h2>view</h2>
            <div class="opener">
                <div class="line1"></div>
                <div class="line2"></div>
            </div>
        </div>            
        <div class="pages-data">
            <div class="inputs">
                <p>Name of your file: </p>
                <input type="text" placeholder="Name:">
            </div>
            <div class="inputs">
                <div class="input-dp">
                    <div class="dp-btn">
                        <p>test</p>
                        <div class="opener-sml">
                            <div class="line1"></div>
                            <div class="line2"></div>
                        </div>
                    </div>
                    <div class="dp-content">
                        <p>.css</p>
                        <p>.html</p>
                        <p>.php</p>
                    </div>
                </div>                    
            </div>  
            <div class="inputs">
                <p>Url of your file: </p>
                <input type="text" placeholder="Url: ">
            </div>
            <div class="inputs">
                <p>Public</p>
                <label class="toggle" for="myToggle">
                    <input class="toggle__input" type="checkbox" id="myToggle">
                    <div class="toggle__fill"></div>
                </label>
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
    const opener_sml = document.querySelector('.opener-sml');
    const dp_content = document.querySelector('.dp-content');
    const pages_options = document.querySelector('.pages-options');

    opener.addEventListener('click', () => {
        opener.classList.toggle('active');
        pages_data.classList.toggle('active');
        pages_options.classList.toggle('active');
    });
    opener_sml.addEventListener('click', () => {
        opener_sml.classList.toggle('active');
        dp_content.classList.toggle('active');
        pages_options.classList.toggle('active2');
    });
</script>