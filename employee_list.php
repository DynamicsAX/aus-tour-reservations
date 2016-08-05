<?php
 
function employee_list() {
    ?>
    <style>
        table {
            border-collapse: collapse; 
        }
 
        table, td, th {
            border: 1px solid black;
            padding: 20px;
            text-align: center;
        }
    </style>
    <div class="wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
					<th>User</th>
                    <th>Role</th>
                    <th>Contact</th>
                </tr>
            </thead>
            <tbody>
				<?php
				global $wpdb;
				$table_name = $wpdb->prefix.'employee_list';
				$employees = $wpdb->get_results("SELECT id,name,user,contact,role from $table_name");
				foreach ($employees as $employee) {
					?>
					<tr>
						<td><?= $employee->id; ?></td>
						<td><?= $employee->name; ?></td>
						<td><?= $employee->user; ?></td>
						<td><?= $employee->role; ?></td>
						<td><?= $employee->contact; ?></td>
					</tr>
				<?php } ?>

			</tbody>
        </table>
    </div>
    <?php 
}
add_shortcode('full_employee_list', 'employee_list');
?>