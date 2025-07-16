<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <p>This is your dashboard. You can manage your files here.</p>
        <a href="/logout" class="btn btn-danger">Logout</a>

        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="/users" class="btn btn-info">Manage Users</a>
        <?php endif; ?>

        <div class="mt-5">
            <h2>Upload File</h2>
            <form action="/upload" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Select file to upload:</label>
                    <input type="file" name="file" id="file" class="form-control-file">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>

        <div class="mt-5">
            <h2>Create Folder</h2>
            <form action="/dashboard" method="post">
                <div class="form-group">
                    <label for="folder_name">Folder Name:</label>
                    <input type="text" name="folder_name" id="folder_name" class="form-control" required>
                </div>
                <button type="submit" name="create_folder" class="btn btn-primary">Create Folder</button>
            </form>
        </div>

        <div class="mt-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                    <?php
                    // TODO: Add breadcrumbs for subfolders
                    ?>
                </ol>
            </nav>
            <div class="row">
                <div class="col-md-6">
                    <h2>My Files</h2>
                </div>
                <div class="col-md-6">
                    <form action="/dashboard" method="get" class="form-inline float-right">
                        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search">
                        <select name="type" class="form-control mr-sm-2">
                            <option value="">All Types</option>
                            <option value="pdf">PDF</option>
                            <option value="docx">DOCX</option>
                            <option value="xlsx">XLSX</option>
                            <option value="jpg">JPG</option>
                            <option value="png">PNG</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Uploaded On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($folder = $folders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $folder['name']; ?></td>
                            <td>Folder</td>
                            <td>-</td>
                            <td><?php echo $folder['created_at']; ?></td>
                            <td>
                                <a href="/dashboard?folder_id=<?php echo $folder['id']; ?>" class="btn btn-sm btn-primary">Open</a>
                                <a href="/dashboard?delete_folder=<?php echo $folder['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php while ($file = $files->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $file['name']; ?></td>
                            <td><?php echo $file['type']; ?></td>
                            <td><?php echo round($file['size'] / 1024, 2); ?> KB</td>
                            <td><?php echo $file['created_at']; ?></td>
                            <td>
                                <a href="/<?php echo $file['path']; ?>" class="btn btn-sm btn-success" download>Download</a>
                                <a href="/share?file_id=<?php echo $file['id']; ?>" class="btn btn-sm btn-info">Share</a>
                                <a href="/dashboard?delete_file=<?php echo $file['id']; ?>" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <h2>Storage Usage</h2>
            <p>You are using 0 MB of your 100 MB quota.</p>
        </div>

        <div class="mt-5">
            <h2>Activity Log</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Target Type</th>
                        <th>Target ID</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $log['action']; ?></td>
                            <td><?php echo $log['target_type']; ?></td>
                            <td><?php echo $log['target_id']; ?></td>
                            <td><?php echo $log['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
