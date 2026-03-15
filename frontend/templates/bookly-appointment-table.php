<?php
/**
 * $items, $cpage, $tpages, $user_email
 */
?>

<div id="bnp-confirm-overlay" class="bnp-overlay">
    <div class="bnp-confirm-box" style="min-height:100px">
       <div id="bnp-confirm-box">
          <p>Are you sure?</p>
          <div class="bnp-confirm-actions">
            <button id="bnp-confirm-yes" class="confirm-button button-primary">Yes</button>
            <button id="bnp-confirm-no" class="confirm-button button">Cancel</button>
          </div>
        </div>
    </div>
</div>
<div id="mycred-msg" class="mycred-msg"></div>
<div class="bnp-pagination" style="margin: 20px 0;">
    <a class="prev-page <?php echo ( $cpage <= 1 ) ? 'disabled' : ''; ?>" 
       data-page="<?php echo max(1, $cpage - 1); ?>" data-status="<?php echo $atts_status ?>"
       href="javascript:void(0);">« Prev</a>

    <span class="page-numbers" style="padding: 0 15px; color: #666;">
        Page <strong><?php echo $cpage; ?></strong> of <?php echo $tpages; ?>
    </span>

    <a class="next-page <?php echo ( $cpage >= $tpages ) ? 'disabled' : ''; ?>" 
       data-page="<?php echo min($tpages, $cpage + 1); ?>" data-status="<?php echo $atts_status ?>"
       href="javascript:void(0);">Next »</a>
</div>
    <table class="bnp-payment-table">
        <thead>
            <tr>
                <tr>
                    <th>Book No</th>
                    <th>Speaker</th>
                    <th>Class</th>
                    <th>Time</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $items as $item ) : 
                    $time   = date( 'Y-m-d H:i', strtotime( $item['start_date'] ) );
                    $status = ucfirst( $item['app_status'] );
            ?>
                <tr>
                    <td>#<?php echo esc_html( $item['book_no'] ); ?></td>
                    <td><?php echo esc_html( $item['staff_name']) ?></td>
                    <td><?php echo esc_html( $item['service_title']) ?></td>
                    <td><?php echo esc_html( $time ); ?></td>
                    <td><?php echo esc_html( $user_email ); ?></td>
                    <td><span class="status-<?php echo strtolower($status); ?>"><?php echo esc_html( $status ); ?></span></td>
                    <td>
                        <?php 
                        $current_time = current_time( 'timestamp' );
                        $start_time   = strtotime( $item['start_date'] );
                    
                        if ( $item['app_status'] === 'approved' ) {
                            if ( $current_time < $start_time ) {
                                ?>    
                                <button type="button" class="cancel-btn button button-secondary"
                                data-id="<?php echo $item['book_no']; ?>" 
                                data-advance="<?php echo $atts_advance; ?>"
                                data-key="<?php echo $atts_key; ?>" 
                                data-type="cancel">
                                    Cancel
                                </button>
                                <?php
                            } else {
                                ?>
                                <button type="button" class="cancel-btn button button-success" 
                                data-id="<?php echo $item['book_no']; ?>"
                                data-key="<?php echo $atts_key; ?>" 
                                data-type="completed">
                                    Complete
                                </button>
                                <?php
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="bnp-pagination" style="margin: 20px 0;">
        <a class="prev-page <?php echo ( $cpage <= 1 ) ? 'disabled' : ''; ?>" 
           data-page="<?php echo max(1, $cpage - 1); ?>" data-status="<?php echo $atts_status ?>"
           href="javascript:void(0);">« Prev</a>
    
        <span class="page-numbers" style="padding: 0 15px; color: #666;">
            Page <strong><?php echo $cpage; ?></strong> of <?php echo $tpages; ?>
        </span>
    
        <a class="next-page <?php echo ( $cpage >= $tpages ) ? 'disabled' : ''; ?>" 
           data-page="<?php echo min($tpages, $cpage + 1); ?>" data-status="<?php echo $atts_status ?>"
           href="javascript:void(0);">Next »</a>
    </div>