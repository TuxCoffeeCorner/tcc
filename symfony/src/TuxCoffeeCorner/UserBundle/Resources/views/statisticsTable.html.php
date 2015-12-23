<tr><td>Diesen Monat</td><td>Dein Favorit</td><td><?php echo $stats['m_fav_name']; ?> (<?php echo $stats['m_fav_count']; ?> mal gekauft)</td></tr>
<tr><td></td><td>Betrag</td><td><?php echo sprintf("%01.2f", $stats['m_sum']);?> CHF</td></tr>
<tr><td></td><td>Platz</td><td><?php echo $stats['m_rank']; ?></td></tr>
<tr><td>Alltime</td><td>Dein Favorit</td><td><?php echo $stats['a_fav_name']; ?> (<?php echo $stats['a_fav_count']; ?> mal gekauft)</td></tr>
<tr><td></td><td>Betrag</td><td><?php echo sprintf("%01.2f", $stats['a_sum']);?> CHF</td></tr>
<tr><td></td><td>Platz</td><td><?php echo $stats['a_rank']; ?></td></tr>