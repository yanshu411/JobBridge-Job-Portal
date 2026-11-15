<?php
/** expects $form, $button_label */
?>
<form method="post" class="surface padded">
  <?= csrf_input() ?>
  <div class="form-grid">
    <div class="form-group"><label class="form-label" for="title">Job title *</label><input class="form-control" id="title" name="title" maxlength="120" value="<?= e($form['title'] ?? '') ?>" placeholder="e.g. Junior Web Developer" required></div>
    <div class="form-group"><label class="form-label" for="company">Company name *</label><input class="form-control" id="company" name="company" maxlength="120" value="<?= e($form['company'] ?? '') ?>" placeholder="e.g. Bright Labs" required></div>
    <div class="form-group"><label class="form-label" for="location">Location *</label><input class="form-control" id="location" name="location" maxlength="120" value="<?= e($form['location'] ?? '') ?>" placeholder="e.g. Delhi, India" required></div>
    <div class="form-group"><label class="form-label" for="category">Category *</label><select class="form-control" id="category" name="category" required><?php foreach (job_categories() as $category): ?><option value="<?= e($category) ?>" <?= ($form['category'] ?? 'Technology') === $category ? 'selected' : '' ?>><?= e($category) ?></option><?php endforeach; ?></select></div>
    <div class="form-group"><label class="form-label" for="job_type">Job type *</label><select class="form-control" id="job_type" name="job_type" required><?php foreach (job_types() as $type): ?><option value="<?= e($type) ?>" <?= ($form['job_type'] ?? 'Full-time') === $type ? 'selected' : '' ?>><?= e($type) ?></option><?php endforeach; ?></select></div>
    <div class="form-group"><label class="form-label" for="work_mode">Work mode *</label><select class="form-control" id="work_mode" name="work_mode" required><?php foreach (work_modes() as $mode): ?><option value="<?= e($mode) ?>" <?= ($form['work_mode'] ?? 'On-site') === $mode ? 'selected' : '' ?>><?= e($mode) ?></option><?php endforeach; ?></select></div>
    <div class="form-group"><label class="form-label" for="salary_min">Minimum monthly salary (₹)</label><input class="form-control" id="salary_min" name="salary_min" type="number" min="0" max="100000000" value="<?= e($form['salary_min'] ?? '') ?>" placeholder="25000"><p class="form-hint">Optional. Use monthly rupee amounts.</p></div>
    <div class="form-group"><label class="form-label" for="salary_max">Maximum monthly salary (₹)</label><input class="form-control" id="salary_max" name="salary_max" type="number" min="0" max="100000000" value="<?= e($form['salary_max'] ?? '') ?>" placeholder="45000"></div>
  </div>
  <div class="form-group"><label class="form-label" for="description">Job description *</label><textarea class="form-control" id="description" name="description" rows="7" minlength="30" maxlength="10000" placeholder="Describe the responsibilities, team and opportunity..." required><?= e($form['description'] ?? '') ?></textarea></div>
  <div class="form-group"><label class="form-label" for="requirements">Skills and requirements</label><textarea class="form-control" id="requirements" name="requirements" rows="5" maxlength="5000" placeholder="e.g. HTML, CSS, JavaScript, communication skills... "><?= e($form['requirements'] ?? '') ?></textarea></div>
  <?php if (isset($form['status'])): ?>
  <div class="form-group"><label class="form-label" for="status">Listing status</label><select class="form-control" id="status" name="status"><option value="open" <?= $form['status'] === 'open' ? 'selected' : '' ?>>Open (accepting applications)</option><option value="closed" <?= $form['status'] === 'closed' ? 'selected' : '' ?>>Closed (not accepting applications)</option></select></div>
  <?php endif; ?>
  <button class="btn btn-primary" type="submit"><?= e($button_label) ?></button> <a class="btn btn-ghost" href="manage_jobs.php">Cancel</a>
</form>
