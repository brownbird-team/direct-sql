<div class="sidebar active">
    <div class="toggle-btn">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="line"></div>
    <div class="links">
        <a href=""><i class="fi fi-rr-user"></i>Account</a>
        <a href="" class="active"><i class="fi fi-rr-settings"></i>Setting</a>
        <a href=""><i class="fi fi-rr-file"></i>Pages</a>
        <a href=""><i class="fi fi-rr-database"></i>PhpmyAdmin</a>
        <a href=""><i class="fi fi-rr-folder"></i>Files</a>
        <a href=""><i class="fi fi-rr-bug"></i>Report bug</a>
    </div>
</div>
<script>
    document.querySelector(".sidebar .toggle-btn").addEventListener("click", function() {
        document.querySelector(".sidebar").classList.toggle("active");
    });
</script>