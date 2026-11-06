</div><!-- .main -->
</div><!-- .layout -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
  $('.dtable').DataTable({
    language: { search: '', searchPlaceholder: 'Rechercher...', lengthMenu: '_MENU_ / page', info: '_START_–_END_ sur _TOTAL_', paginate: { previous: '←', next: '→' }, zeroRecords: 'Aucun résultat', emptyTable: 'Aucune donnée disponible' },
    pageLength: 10,
    dom: '<"dt-top"lf>rt<"dt-bottom"ip>'
  });
  const params = new URLSearchParams(window.location.search);
  const msgs = { added: 'Ajout réussi', updated: 'Modification enregistrée', deleted: 'Supprimé', status_updated: 'Statut mis à jour', role_changed: 'Rôle modifié', stock_added: 'Stock mis à jour', coupon_added: 'Coupon créé', sent: 'Newsletter envoyée' };
  const key = params.get('success') || (params.get('added') && 'added') || (params.get('updated') && 'updated') || (params.get('deleted') && 'deleted') || (params.get('sent') && 'sent');
  if(key && msgs[key]) showToast(msgs[key]);
  window.history.replaceState({}, '', window.location.pathname);
});
// Rafraîchissement des alertes stock (toutes les 30s)
setInterval(function() {
  fetch('check_notifications.php')
    .then(r => r.json())
    .then(data => {
      let badge = document.querySelector('.tb-icon .badge-notif');
      if (data.low_stock > 0) {
        if(badge) badge.textContent = data.low_stock;
        else {
          let newBadge = document.createElement('span');
          newBadge.className = 'badge-notif';
          newBadge.style.cssText = 'position:absolute; top:-5px; right:-5px; background:#ef4444; color:white; font-size:9px; padding:2px 5px; border-radius:50%;';
          newBadge.textContent = data.low_stock;
          document.querySelector('.tb-icon').appendChild(newBadge);
        }
      } else if(badge) badge.remove();
    });
}, 30000);
</script>
</body>
</html>