                                <?php foreach ($row_sec as $r) :
                                  if ($row['member_security_q_id'] == $r['member_security_q_cat']) : ?>
                                    <option value="<?= $r['member_security_q_id'] ?>"><?= $r['member_security_q'] ?></option>
                                    <!-- WEEKEND START HERE -->
                                <?php endif;
                                endforeach; ?>


                                <?php foreach ($row_sec as $r) :
                                  if ($row['member_security_q_id'] == $r['member_security_q_cat']) : ?>
                                    <option value="<?= $r['member_security_q_id'] ?>" <?php if ($row['cat2'] == $r['member_security_q_id']) echo 'selected' ?>><?= $r['member_security_q'] ?></option>
                                <?php endif;
                                endforeach; ?>