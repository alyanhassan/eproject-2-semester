    </div><!-- /.dash-content -->
  </main><!-- /.dash-main -->
</div><!-- /.dash-wrapper -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded',function(){
  var t=document.getElementById('sidebarToggle');
  var s=document.querySelector('.sidebar');
  if(t&&s){t.style.display='block';t.addEventListener('click',function(){s.classList.toggle('open');});}
});
</script>
</body>
</html>
