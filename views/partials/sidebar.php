<div class="sidebar active">
    <div class="toggle-btn">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="links">
        <a href="#">Account</a>
        <a href="#" class="active">Setting</a>
        <a href="#">Pages</a>
        <a href="#">PhpmyAdmin</a>
        <a href="#">Files</a>
        <a href="#">Report bug</a>
    </div>
</div>
<script>
    document.querySelector(".sidebar .toggle-btn").addEventListener("click", function() {
        document.querySelector(".sidebar").classList.toggle("active");
    });
</script>