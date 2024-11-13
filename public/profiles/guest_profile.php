<div>
    <div>
        <h2>Your Home Page</h2>
        <p>Velkommen, <?php echo htmlspecialchars($userProfile->getUsername()); ?>!</p>
        <p>Email: <?php echo htmlspecialchars($userProfile->getEmail()); ?></p>
    </div>
</div>
