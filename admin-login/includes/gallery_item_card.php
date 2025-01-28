<div class="col-md-6 col-lg-4 gallery-item" data-id="<?php echo $item['id']; ?>">
    <div class="card h-100">
        <div class="card-img-top position-relative">
            <img src="../../uploads/gallery/<?php echo htmlspecialchars($item['image']); ?>" 
                 class="img-fluid" alt="<?php echo htmlspecialchars($item['title']); ?>">
            <div class="sort-handle position-absolute top-0 start-0 m-2 cursor-move">
                <i class="bi bi-grip-vertical text-white"></i>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
            <?php if (!empty($item['description'])): ?>
                <p class="card-text small"><?php echo htmlspecialchars($item['description']); ?></p>
            <?php endif; ?>
        </div>
        <div class="card-footer bg-transparent">
            <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-sm btn-outline-primary" 
                        onclick="editGalleryItem(<?php echo $item['id']; ?>)">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" 
                        onclick="deleteGalleryItem(<?php echo $item['id']; ?>)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>
