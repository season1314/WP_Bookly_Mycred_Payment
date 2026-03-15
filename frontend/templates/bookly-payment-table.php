<?php
/**
 * $items, $cpage, $tpages, $user_email
 */
?>
<div class="bnp-pagination" style="margin: 20px 0;">
    <a class="prev-page <?php echo ( $cpage <= 1 ) ? 'disabled' : ''; ?>" 
       data-page="<?php echo max(1, $cpage - 1); ?>" 
       href="javascript:void(0);">« Prev</a>

    <span class="page-numbers" style="padding: 0 15px; color: #666;">
        Page <strong><?php echo $cpage; ?></strong> of <?php echo $tpages; ?>
    </span>

    <a class="next-page <?php echo ( $cpage >= $tpages ) ? 'disabled' : ''; ?>" 
       data-page="<?php echo min($tpages, $cpage + 1); ?>" 
       href="javascript:void(0);">Next »</a>
</div>
    <table class="bnp-payment-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date (NZDT)</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $items as $item ) : 
                $amount = number_format( $item['total'], 2 );
                $date   = date( 'Y-m-d H:i', strtotime( $item['created_at'] ) );
                $status = ucfirst( $item['status'] );
                $checkout_url = site_url($redirect_url . '?pid=' . esc_attr( $item['id'] ));
            ?>
                <tr>
                    <td>#<?php echo esc_html( $item['id'] ); ?></td>
                    <td><?php echo $amount; ?> Credits</td>
                    <td><span class="status-<?php echo strtolower($status); ?>"><?php echo esc_html( $status ); ?></span></td>
                    <td><?php echo esc_html( $date ); ?></td>
                    <td><?php echo esc_html( $user_email ); ?></td>
                    <td>
                        <?php if ( $item['status'] === 'pending' ) : ?>
                            <button type="button" class="pay-now-btn button button-primary" 
                                    onclick="window.location.href='<?php echo esc_url( $checkout_url ); ?>'">
                                Pay Now
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="bnp-pagination" style="margin: 20px 0;">
        <a class="prev-page <?php echo ( $cpage <= 1 ) ? 'disabled' : ''; ?>" 
           data-page="<?php echo max(1, $cpage - 1); ?>" 
           href="javascript:void(0);">« Prev</a>
    
        <span class="page-numbers" style="padding: 0 15px; color: #666;">
            Page <strong><?php echo $cpage; ?></strong> of <?php echo $tpages; ?>
        </span>
    
        <a class="next-page <?php echo ( $cpage >= $tpages ) ? 'disabled' : ''; ?>" 
           data-page="<?php echo min($tpages, $cpage + 1); ?>" 
           href="javascript:void(0);">Next »</a>
    </div>