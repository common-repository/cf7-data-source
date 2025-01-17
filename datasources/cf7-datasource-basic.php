<?php
require_once dirname( __FILE__ ) . '/cf7-datasource-base.php';
if ( ! class_exists( 'CF7_DATASOURCE_BASIC' ) ) {
	class CF7_DATASOURCE_BASIC extends CF7_DATASOURCE_BASE {

		public function __construct( $manager ) {
			parent::__construct( $manager );
		} // End __construct

		public function default_attributes() {
			$attributes = array(
				/* POST */

				'attributes'   => 'ID', // Attributes to get separated by comma, like post_title, post_author
				'condition'    => '', // Condition for filtering like post_status='publish' AND post_author=1
				'current_post' => false, // Page or post where the form is inserted

				/* TAXONOMY */

				'taxonomy'     => '', // Taxonomy name. Ex. category
				// 'attributes' => 'name',
									/*
									 attributes is commented because this attribute was included above.
									The attributes must be separated by comma, like term_id, name, slug */
				// 'condition'  => '',
									/*
									 condition is commented because this attribute was included above.
									Ex. term_id = 1 OR slug='uncategorized' */
				'order_by'     => '', // column for sorting the terms
				'in'           => '', // Posts ids separated by commas

				/* USER */

				// 'attributes' => 'ID',
									/*
									 attributes is commented because this attribute was included above.
									The attributes must be separated by comma, like nicename, email, first_name */
				'logged'       => 0, // To get the data corresponding to the logged user enter 1
				// 'condition'  => '',
									/*
									 condition is commented because this attribute was included above.
									Ex. user_login='admin'
									If the logged attribute is set to 1 it takes precedence */

				/* DATABASE */

				'dns'          => '',
				'engine'       => 'mysql',
				'hostname'     => '',
				'database'     => '',
				'username'     => '',
				'password'     => '',

				'query'        => '',

				/* CLIENT SIDE */

				// 'function'  => '', // attribute inherited, client side function name
				// 'parameters'=> '', // attribute inherited, function parameters
			);

			return array_merge( parent::default_attributes(), $attributes );
		} // End default_attributes

		public function ds_options() {
			return '<option value="url-parameters">' . esc_html__( 'URL Parameters', 'cf7-datasource' ) . '</option>
					<option value="user">' . esc_html__( 'Users information', 'cf7-datasource' ) . '</option>
					<option value="database" SELECTED>' . esc_html__( 'Read data from database', 'cf7-datasource' ) . '</option>
					<option value="post">' . esc_html__( 'Posts data (posts, pages, products, custom post types)', 'cf7-datasource' ) . '</option>
					<option value="taxonomy">' . esc_html__( 'Taxonomies (categories, posts tags, custom taxonomies)', 'cf7-datasource' ) . '</option>
					<option value="client">' . esc_html__( 'Javascript function', 'cf7-datasource' ) . '</option>';
		} // End ds_options

		public function ds_settings() {
			global $wpdb;

			// cf7-users-datalist
			$users_datalist_options = '';
			$users_order_by_options = '';

			$rows = $wpdb->get_results( 'SHOW COLUMNS FROM `' . $wpdb->users . '`' );
			foreach ( $rows as $row ) {
				$users_datalist_options .= '<option value="' . esc_attr( $row->Field ) . '" />';
				$users_order_by_options .= '<option value="' . esc_attr( $row->Field ) . '">' . esc_html( $row->Field ) . '</option>';
			}

			$rows = $wpdb->get_results( 'SELECT DISTINCT meta_key FROM `' . $wpdb->usermeta . '`' );
			foreach ( $rows as $row ) {
				$users_datalist_options .= '<option value="' . esc_attr( $row->meta_key ) . '" />';
			}

			// cf7-posts-datalist
			$posts_datalist_options = '';
			$posts_order_by_options = '';

			$rows = $wpdb->get_results( 'SHOW COLUMNS FROM `' . $wpdb->posts . '`' );
			foreach ( $rows as $row ) {
				$posts_datalist_options .= '<option value="' . esc_attr( $row->Field ) . '" />';
				$posts_order_by_options .= '<option value="' . esc_attr( $row->Field ) . '">' . esc_html( $row->Field ) . '</option>';
			}

			// cf7-taxonomies-list
			$taxonomies_datalist_options = '';
			$taxonomies                  = get_taxonomies( array(), 'names' );
			foreach ( $taxonomies as $taxonomy ) {
				$taxonomies_datalist_options .= '<option value="' . esc_attr( $taxonomy ) . '" />';
			}

			return '<!-- Data source url parameters -->
					<style>
						.tag-generator-panel{max-height:485px;}
						[data-id="cf7-recordset"] .insert-box{box-sizing:border-box;min-height:40px;}
					</style>
					<tr class="cf7-datasource-url-parameters">
						<th scope="row"></th>
						<td>
							<p>' . esc_html__( 'Creates a record with the URL parameters as its properties.', 'cf7-datasource' ) . '</p>
							<br><a href="https://cf7-datasource.dwbooster.com/documentation#url-parameters" target="_blank" class="button-secondary">' . __( 'Data surce HELP', 'cf7-datasource' ) . '</a>
						</td>
					</tr>

					<!-- Data source user -->
					<tr class="cf7-datasource-user">
						<th scope="row">' . esc_html__( 'User data separated by comma', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'User data', 'cf7-datasource' ) . '" type="text" name="cf7-user-attributes" list="cf7-users-datalist" />
                            <datalist id="cf7-users-datalist">
                            ' . $users_datalist_options . '
                            </datalist>
							<br><i>Ex. user_email,user_login,first_name</i>
						</td>
					</tr>
					<tr class="cf7-datasource-user">
						<th scope="row">' . esc_html__( 'Get data of logged user', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'For logged user', 'cf7-datasource' ) . '" type="checkbox" name="cf7-user-logged" />
						</td>
					</tr>
					<tr class="cf7-datasource-user">
						<th scope="row">' . esc_html__( 'Condition for filtering', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Condition for filtering', 'cf7-datasource' ) . '" type="text" name="cf7-user-condition" />
							<br><i>Ex. user_email=\'{field.your-email}\'</i>
						</td>
					</tr>
					<tr class="cf7-datasource-user">
						<th scope="row">' . esc_html__( 'Order by', 'cf7-datasource' ) . '</th>
						<td>
							<select aria-label="' . esc_attr__( 'Order by', 'cf7-datasource' ) . '" type="text" name="cf7-user-order-by">
							' . $users_order_by_options . '
							</select>
						</td>
					</tr>
					<tr class="cf7-datasource-user">
						<th scope="row"></th>
						<td style="text-align:right;">
                            <a href="https://cf7-datasource.dwbooster.com/documentation#constants" target="_blank" style="float:left;">' . __( 'Constants', 'cf7-datasource' ) . '</a>
                            <button class="button-primary cf7-recordset-test">' . __( 'Test data source', 'cf7-datasource' ) . '</button>
                            <a href="https://cf7-datasource.dwbooster.com/documentation#user-information" target="_blank" class="button-secondary">' . __( 'Data surce HELP', 'cf7-datasource' ) . '</a>
						</td>
					</tr>

					<!-- Data source post -->
					<tr class="cf7-datasource-post">
						<th scope="row">' . esc_html__( 'Post data separated by comma', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Post data', 'cf7-datasource' ) . '" type="text" name="cf7-post-attributes" list="cf7-posts-datalist" />
                            <datalist id="cf7-posts-datalist">
                            ' . $posts_datalist_options . '
                            </datalist>
							<br><i>Ex. ID,post_title,post_excerpt,post_status</i>
						</td>
					</tr>
					<tr class="cf7-datasource-post">
						<th scope="row">' . esc_html__( 'Condition for filtering', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Condition for filtering', 'cf7-datasource' ) . '" type="text" name="cf7-post-condition" />
							<br><i>Ex. post_status=\'publish\' AND post_author=1</i>
                            <br><a href="https://cf7-datasource.dwbooster.com/documentation#constants" target="_blank" style="float:left;">' . __( 'Constants', 'cf7-datasource' ) . '</a>
						</td>
					</tr>
					<tr class="cf7-datasource-post">
						<th scope="row">' . esc_html__( 'Order by', 'cf7-datasource' ) . '</th>
						<td>
							<select aria-label="' . esc_attr__( 'Order by', 'cf7-datasource' ) . '" name="cf7-post-order-by" />
							' . $posts_order_by_options . '
							</select>
						</td>
					</tr>
					<tr class="cf7-datasource-post">
						<th scope="row"></th>
						<td>
							<label><input aria-label="' . esc_attr__( 'Current post', 'cf7-datasource' ) . '" type="checkbox" name="cf7-post-current" />&nbsp;' . __( 'Current post/page (where the form is inserted)', 'cf7-datasource' ) . '</label>
						</td>
					</tr>
					<tr class="cf7-datasource-post">
						<th scope="row"></th>
						<td style="text-align:right;">
                            <button class="button-primary cf7-recordset-test">' . __( 'Test data source', 'cf7-datasource' ) . '</button>
                            <a href="https://cf7-datasource.dwbooster.com/documentation#post-data" target="_blank" class="button-secondary">' . __( 'Data surce HELP', 'cf7-datasource' ) . '</a>
						</td>
                    </tr>

					<!-- Data source taxonomy -->
					<tr class="cf7-datasource-taxonomy">
						<th scope="row">' . esc_html__( 'Taxonomy name', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Taxonomy name', 'cf7-datasource' ) . '" type="text" name="cf7-taxonomy-name" list="cf7-taxonomies-list" />
                            <datalist id="cf7-taxonomies-list">
                            ' . $taxonomies_datalist_options . '
                            </datalist>
							<br><i>Ex. category</i>
						</td>
					</tr>
					<tr class="cf7-datasource-taxonomy">
						<th scope="row">' . esc_html__( 'Terms data separated by comma', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Terms data', 'cf7-datasource' ) . '" type="text" name="cf7-taxonomy-attributes" />
							<br><i>Ex. term_id,name,slug</i>
						</td>
					</tr>
					<tr class="cf7-datasource-taxonomy">
						<th scope="row">' . esc_html__( 'Condition for filtering', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Condition for filtering', 'cf7-datasource' ) . '" type="text" name="cf7-taxonomy-condition" />
							<br><i>Ex. term_id=1 OR slug=\'uncategorized\'</i>
						</td>
					</tr>
					<tr class="cf7-datasource-taxonomy">
						<th scope="row">' . esc_html__( 'Order by', 'cf7-datasource' ) . '</th>
						<td>
							<select aria-label="' . esc_attr__( 'Order by', 'cf7-datasource' ) . '" name="cf7-taxonomy-order-by">
								<option value="term_id">term_id</option>
								<option value="name">name</option>
								<option value="slug">slug</option>
							</select>
						</td>
					</tr>
					<tr class="cf7-datasource-taxonomy">
						<th scope="row">' . esc_html__( 'In pages/posts', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'In pages/posts', 'cf7-datasource' ) . '" type="text" name="cf7-taxonomy-posts" />
							<br><i>Pages and posts ids, separated by comma, or {post.id} constant for the current post id.</i>
						</td>
					</tr>
					<tr class="cf7-datasource-taxonomy">
						<th scope="row"></th>
						<td style="text-align:right;">
                            <a href="https://cf7-datasource.dwbooster.com/documentation#constants" target="_blank" style="float:left;">' . __( 'Constants', 'cf7-datasource' ) . '</a>
                            <button class="button-primary cf7-recordset-test">' . __( 'Test data source', 'cf7-datasource' ) . '</button>
                            <a href="https://cf7-datasource.dwbooster.com/documentation#taxonomy" target="_blank" class="button-secondary">' . __( 'Data surce HELP', 'cf7-datasource' ) . '</a>
						</td>
					</tr>

                    <!-- Data source database -->
					<tr class="cf7-datasource-database">
						<th scope="row">' . esc_html__( 'Database connection', 'cf7-datasource' ) . '</th>
						<td>
							<label><input aria-label="' . esc_attr__( 'Current website database', 'cf7-datasource' ) . '" type="radio" name="cf7-database-connection" value="website" CHECKED /> ' . __( 'Current website database', 'cf7-datasource' ) . '</label>
							<label><input aria-label="' . esc_attr__( 'Connection components', 'cf7-datasource' ) . '" type="radio" name="cf7-database-connection" value="components" /> ' . __( 'Connection components', 'cf7-datasource' ) . '</label>
							<label><input aria-label="' . esc_attr__( 'DNS', 'cf7-datasource' ) . '" type="radio" name="cf7-database-connection" value="dns" /> ' . __( 'DNS', 'cf7-datasource' ) . '</label>
                            <a href="https://cf7-datasource.dwbooster.com/examples" target="_blank" style="float:right;">' . __( 'Get demos +', 'cf7-datasource' ) . '</a>
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-dns cf7-datasource-components">
						<th scope="row"></th>
						<td>
							<b>' . esc_html__( 'To read data from the current website database, leave the connection data blank', 'cf7-datasource' ) . '</b>
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-dns">
						<th scope="row">' . esc_html__( 'DNS', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'DNS', 'cf7-datasource' ) . '" type="text" name="cf7-database-dns" />
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-components">
						<th scope="row">' . esc_html__( 'Engine', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Engine', 'cf7-datasource' ) . '" type="text" name="cf7-database-engine" value="mysql" />
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-components">
						<th scope="row">' . esc_html__( 'Hostname', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Hostname', 'cf7-datasource' ) . '" type="text" name="cf7-database-hostname" />
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-components">
						<th scope="row">' . esc_html__( 'Database name', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Database name', 'cf7-datasource' ) . '" type="text" name="cf7-database-database" />
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-dns cf7-datasource-components">
						<th scope="row">' . esc_html__( 'Database username', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Database username', 'cf7-datasource' ) . '" type="text" name="cf7-database-username" />
						</td>
					</tr>
					<tr class="cf7-datasource-database cf7-datasource-dns cf7-datasource-components">
						<th scope="row">' . esc_html__( 'Database password', 'cf7-datasource' ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Database password', 'cf7-datasource' ) . '" type="text" name="cf7-database-password" />
						</td>
					</tr>
					<tr class="cf7-datasource-database">
						<th scope="row">' .
							esc_html__( 'Enter Query', 'cf7-datasource' ) . '<br>' .
							'(' . esc_html__( '- or - select a predefined one', 'cf7-datasource' ) . ')' .
						'</th>
						<td>
							<textarea rows="3" aria-label="' . esc_attr__( 'Query', 'cf7-datasource' ) . '" type="text" name="cf7-database-query"></textarea>
							<br><i>Ex. SELECT ID,post_title FROM {wpdb.posts} WHERE post_status=\'publish\'</i>
						</td>
					</tr>
					<tr class="cf7-datasource-database">
						<th scope="row">' . esc_html__( 'Predefined queries', 'cf7-datasource' ) . '</th>
						<td>
                            <div class="cf7-ds-predefined-container">
								<select name="cf7-database-predefined-query">
									<optgroup label="' . esc_attr__( 'User-related queries', 'cf7-datasource' ) . '">
										<option data-description="' . esc_attr__( 'Returns the list of users records with five properties (id, username, email, first_name, last_name)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT user.ID AS id, user.user_login AS username, user.user_email AS email, meta1.meta_value AS first_name, meta2.meta_value AS last_name FROM {wpdb.users} user LEFT JOIN {wpdb.usermeta} meta1 ON (user.ID = meta1.user_id AND meta1.meta_key="first_name") LEFT JOIN {wpdb.usermeta} meta2 ON (user.ID = meta2.user_id AND meta2.meta_key="last_name")' ) . '">' . esc_html__( 'Users Names and Emails', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of users records with four properties (id, username, email, display_name). Replace the the-role text in the query with the role name for filtering', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT u.ID as id, u.user_login as username, u.user_email as email, u.display_name as display_name FROM {wpdb.users} u JOIN {wpdb.usermeta} um ON u.ID = um.user_id WHERE um.meta_key = "wp_capabilities" AND um.meta_value LIKE "%the-role%";' ) . '">' . esc_html__( 'User by Role', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of user\'s metadata based on user\'s ID. Returns a list of records with two properties (metadata, value). Replace the "the-user-id" in the query with user\'s ID for filtering', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT meta_key as metadata, meta_value as value FROM {wpdb.usermeta} WHERE user_id=the-user-id;' ) . '">' . esc_html__( 'User metadata by ID', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of published posts by the author ID. Each record contains four properties (post_id, post_title, post_excerpt, post_url). Replace the "the-user-id" in the query with user\'s ID for filtering', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT post.ID AS post_id, post.post_title AS post_title, post.post_excerpt AS post_excerpt, post.guid AS post_url FROM {wpdb.posts} post WHERE post.post_status="publish" AND post_type="post" AND post.post_author=the-author-id ORDER BY post_title;' ) . '">' . esc_html__( 'Published posts by author ID', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of approved comments by the author name. Each record contains five properties (comment_content, comment_date, post_id, post_title, post_url). Replace the "author-name" in the query with corresponding author name for filtering the comments', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT comments.comment_content as comment_content, comments.comment_date as comment_date, comments.comment_post_ID as post_id, posts.post_title as post_title, posts.guid as post_url FROM {wpdb.comments} comments INNER JOIN {wpdb.posts} posts ON (comments.comment_post_ID = posts.ID) WHERE posts.post_status="publish" AND comments.comment_approved=1 AND comments.comment_author="author-name";' ) . '">' . esc_html__( 'Approved comments by author name', 'cf7-datasource' ) . '</option>
									</optgroup>

									<optgroup label="' . esc_attr__( 'Posts and comments related queries', 'cf7-datasource' ) . '">
										<option data-description="' . esc_attr__( 'Returns the list of published posts and their authors records with six properties (post_id, post_title, post_excerpt, post_url, author_id, author_name)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT post.ID AS post_id, post.post_title AS post_title, post.post_excerpt AS post_excerpt, post.guid AS post_url, user.ID AS author_id, CONCAT(usermeta1.meta_value, " ", usermeta2.meta_value) AS author_name FROM {wpdb.posts} post INNER JOIN {wpdb.users} user ON (user.ID = post.post_author) LEFT JOIN {wpdb.usermeta} usermeta1 ON (user.ID = usermeta1.user_id AND usermeta1.meta_key="first_name") LEFT JOIN {wpdb.usermeta} usermeta2 ON (user.ID = usermeta2.user_id AND usermeta2.meta_key="last_name") WHERE post.post_status="publish" AND post.post_type="post"' ) . '">' . esc_html__( 'Posts and Authors', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of scheduled posts sorted by date. Records with six properties (post_id, post_title, post_type, post_excerpt, post_url, post_date)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID as post_id, post_title, post_type, post_excerpt, guid AS post_url, post_date FROM {wpdb.posts} WHERE post_status = \'future\' AND post_date >= CURDATE() ORDER BY post_date ASC;' ) . '">' . esc_html__( 'Scheduled Posts', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Retrieves a list of published posts and pages that contain specific text in their titles. Records with four properties (post_id, post_title, post_excerpt, post_url). Replace the search-term text in the query with the text to search in the titles of posts and pages.', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID as post_id, post_title, post_type, post_excerpt, guid AS post_url FROM {wpdb.posts} WHERE post_status = \'publish\' AND post_type IN (\'post\',\'page\') AND post_title LIKE \'%search-term%\' ORDER BY post_title ASC;' ) . '">' . esc_html__( 'List of published pages and posts with specific text in their titles', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Retrieves a list of published posts and pages that contain specific text in their contents. Records with four properties (post_id, post_title, post_excerpt, post_url). Replace the search-term text in the query with the text to search in the contents of posts and pages.', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID as post_id, post_title, post_type, post_excerpt, guid AS post_url FROM {wpdb.posts} WHERE post_status = \'publish\' AND post_type IN (\'post\',\'page\') AND post_content LIKE \'%search-term%\' ORDER BY post_title ASC;' ) . '">' . esc_html__( 'List of published pages and posts with specific text in their contents', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of approved comments and their authors. The records contains six properties (comment_content, comment_author, comment_date, post_id, post_title, post_url)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT comments.comment_content as comment_content, comments.comment_author as comment_author, comments.comment_date as comment_date, comments.comment_post_ID as post_id, posts.post_title as post_title, posts.guid as post_url FROM {wpdb.comments} comments INNER JOIN {wpdb.posts} posts ON (comments.comment_post_ID = posts.ID) WHERE posts.post_status="publish" AND comments.comment_approved=1;' ) . '">' . esc_html__( 'Comments and Authors', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of posts associated with a tag. Replace the "your-tag-slug" text in the query with the tag slug', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT p.ID, p.post_title, p.post_excerpt, p.post_content, p.post_date  FROM {wpdb.posts} p  JOIN {wpdb.term_relationships} tr ON p.ID = tr.object_id  JOIN {wpdb.term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id  JOIN {wpdb.terms} t ON tt.term_id = t.term_id  WHERE tt.taxonomy=\'post_tag\' AND t.slug = \'your-tag-slug\'  AND p.post_type = \'post\'  AND p.post_status = \'publish\'  ORDER BY p.post_date DESC;' ) . '">' . esc_html__( 'Posts by Tag', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of posts associated with a category. Replace the "your-tag-slug" text in the query with the category term slug', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT p.ID, p.post_title, p.post_excerpt, p.post_content, p.post_date  FROM {wpdb.posts} p  JOIN {wpdb.term_relationships} tr ON p.ID = tr.object_id  JOIN {wpdb.term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id  JOIN {wpdb.terms} t ON tt.term_id = t.term_id  WHERE tt.taxonomy=\'category\' AND t.slug = \'your-term-slug\'  AND p.post_type = \'post\'  AND p.post_status = \'publish\'  ORDER BY p.post_date DESC;' ) . '">' . esc_html__( 'Posts by Category', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of posts associated with a custom taxonomy terms. Replace the "your-taxonomy-name" and "your-term-slug" texts in the query with the taxonomy name and its term slug', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT p.ID, p.post_title, p.post_excerpt, p.post_content, p.post_date  FROM {wpdb.posts} p  JOIN {wpdb.term_relationships} tr ON p.ID = tr.object_id  JOIN {wpdb.term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id  JOIN {wpdb.terms} t ON tt.term_id = t.term_id  WHERE tt.taxonomy=\'your-taxonomy-name\' AND t.slug = \'your-term-slug\'  AND p.post_type = \'post\'  AND p.post_status = \'publish\'  ORDER BY p.post_date DESC;' ) . '">' . esc_html__( 'Posts by Custom Taxonomy Term', 'cf7-datasource' ) . '</option>

									</optgroup>

									<optgroup label="' . esc_attr__( 'WooCommerce-related queries', 'cf7-datasource' ) . '">
										<option data-description="' . esc_attr__( 'Returns the list of products records with two properties (id, title)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID AS id, post_title AS title FROM {wpdb.posts} WHERE post_type="product" AND post_status="publish"' ) . '">' . esc_html__( 'WooCommerce Products', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of products purchased by the registered user (id, url, title, excerpt, content)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID AS id, guid AS url, post_title AS title, post_excerpt AS excerpt, post_content AS content FROM {wpdb.posts} WHERE ID in ( SELECT DISTINCT itemmeta.meta_value FROM {wpdb.prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {wpdb.prefix}woocommerce_order_items items ON itemmeta.order_item_id = items.order_item_id INNER JOIN {wpdb.posts} orders ON orders.ID = items.order_id INNER JOIN {wpdb.postmeta} ordermeta ON orders.ID = ordermeta.post_id WHERE itemmeta.meta_key = \'_product_id\' AND ordermeta.meta_key = \'_customer_user\' AND ordermeta.meta_value = {user.id} )' ) . '">' . esc_html__( 'WC Purchases By Registered User', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of products purchased with total revenue per product (product_id, product_name, revenue)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT p.ID AS product_id, p.post_title AS product_name, SUM(oim_total.meta_value) AS revenue FROM {wpdb.posts} p, {wpdb.posts} o, {wpdb.prefix}woocommerce_order_itemmeta oim_product, {wpdb.prefix}woocommerce_order_itemmeta oim_total, {wpdb.prefix}woocommerce_order_items oi WHERE p.ID = oim_product.meta_value AND oim_product.meta_key = \'_product_id\' AND oim_product.order_item_id = oim_total.order_item_id AND oim_total.meta_key = \'_line_total\' AND oi.order_item_id = oim_product.order_item_id AND o.ID = oi.order_id AND o.post_status = \'wc-completed\' GROUP BY p.ID ORDER BY revenue DESC;' ) . '">' . esc_html__( 'WC Purchased Products and Total Revenue per Product', 'cf7-datasource' ) . '</option>

										<option data-description="' . esc_attr__( 'Returns the list of billing address components (billing_first_name, billing_last_name, billing_company, billing_address_1, billing_address_2, billing_city, billing_state, billing_postcode, billing_country)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT DISTINCT pm1.meta_value AS billing_first_name, pm2.meta_value AS billing_last_name, pm3.meta_value AS billing_company, pm4.meta_value AS billing_address_1, pm5.meta_value AS billing_address_2, pm6.meta_value AS billing_city, pm7.meta_value AS billing_state, pm8.meta_value AS billing_postcode, pm9.meta_value AS billing_country FROM {wpdb.posts} p LEFT JOIN {wpdb.postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = \'_billing_first_name\' LEFT JOIN {wpdb.postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = \'_billing_last_name\' LEFT JOIN {wpdb.postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = \'_billing_company\' LEFT JOIN {wpdb.postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = \'_billing_address_1\' LEFT JOIN {wpdb.postmeta} pm5 ON p.ID = pm5.post_id AND pm5.meta_key = \'_billing_address_2\' LEFT JOIN {wpdb.postmeta} pm6 ON p.ID = pm6.post_id AND pm6.meta_key = \'_billing_city\' LEFT JOIN {wpdb.postmeta} pm7 ON p.ID = pm7.post_id AND pm7.meta_key = \'_billing_state\' LEFT JOIN {wpdb.postmeta} pm8 ON p.ID = pm8.post_id AND pm8.meta_key = \'_billing_postcode\' LEFT JOIN {wpdb.postmeta} pm9 ON p.ID = pm9.post_id AND pm9.meta_key = \'_billing_country\' WHERE p.post_type = \'shop_order\';' ) . '">' . esc_html__( 'WC Billing Addresses', 'cf7-datasource' ) . '</option>
									</optgroup>

									<optgroup label="' . esc_attr__( 'Easy Digital Downloads-related queries', 'cf7-datasource' ) . '">
										<option data-description="' . esc_attr__( 'Returns the list of downloads records with two properties (id, title)', 'cf7-datasource' ) . '" value="' . esc_attr( 'SELECT ID AS id, post_title AS title FROM {wpdb.posts} WHERE post_type="download" AND post_status="publish"' ) . '">' . esc_html__( 'EDD Downloads', 'cf7-datasource' ) . '</option>
									</optgroup>
								</select>
								<button type="button" class="button-primary" name="cf7-database-use-predefined-query">' . esc_html__( 'Use It', 'cf7-datasource' ) . '</button>
							</div>
							<div class="cf7-database-predefined-query-description"></div>
							<hr />
						</td>
					</tr>
					<tr class="cf7-datasource-database">
						<th scope="row"></th>
						<td style="text-align:right;">
                            <a href="https://cf7-datasource.dwbooster.com/documentation#constants" target="_blank" style="float:left;">' . esc_html__( 'Constants', 'cf7-datasource' ) . '</a>
                            <button class="button-primary cf7-recordset-test">' . esc_html__( 'Test data source', 'cf7-datasource' ) . '</button>
                            <a href="https://cf7-datasource.dwbooster.com/documentation#database" target="_blank" class="button-secondary">' . esc_html__( 'Data surce HELP', 'cf7-datasource' ) . '</a>
						</td>
					</tr>

					<!-- Data source client side -->
                    <tr class="cf7-datasource-client">
                        <th scope="row">' . esc_html( __( 'Javascript function name', 'cf7-datasource' ) ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Function name', 'cf7-datasource' ) . '" type="text" name="cf7-client-function" />
						</td>
                    </tr>
                    <tr class="cf7-datasource-client">
                        <th scope="row">' . esc_html( __( 'Parameters', 'cf7-datasource' ) ) . '</th>
						<td>
							<input aria-label="' . esc_attr__( 'Parameters', 'cf7-datasource' ) . '" type="text" name="cf7-client-parameters" />
							<br><i>Ex. {field.email},{field.zipcode},{value.plain text},{value.123} (' . esc_html__( 'direct values as parameters, like texts or numbers, use the structure {value.text or number}', 'cf7-datasource' ) . ')</i>
						</td>
					</tr>';
		} // End ds_settings

		public function post_ds( $settings, $data ) {
			global $wpdb;

			$results = array();

			if ( ! empty( $settings['attributes'] ) ) {
				// Replace parameters on conditions
				$condition = $settings['condition'];
				$condition = $this->replace_vars( $condition, $data );
				$condition = $this->replace_constants( $condition );
				$condition = str_replace( array( '&&', '||' ), array( ' AND ', ' OR ' ), $condition );

				$query  = 'SELECT ' . $settings['attributes'] . ' FROM ' . $wpdb->posts . ' WHERE 1=1' . ( ! empty( $condition ) ? ' AND ' . $condition : '' );

				$query .=  ' ORDER BY ' . ( ! empty( $settings['order_by'] ) ? sanitize_text_field( wp_unslash( $settings['order_by'] ) ) : 'ID' );

				$results = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				if ( $wpdb->last_error ) {
					$this->error_log( $wpdb->last_error );
				}
			} else {
				$this->error_log( 'The attributes are required' );
			}
			return $results;
		} // End post_ds

		public function taxonomy_ds( $settings, $data ) {
			global $wpdb;

			$results = array();

			if ( ! empty( $settings['attributes'] ) ) {
				if ( ! empty( $settings['taxonomy'] ) ) {
					// Replace parameters on conditions
					$condition  = $settings['condition'];
					$condition .= ( ! empty( $condition ) ? ' AND ' : '' ) . 'taxonomy="' . esc_sql( $settings['taxonomy'] ) . '"';
					$condition  = $this->replace_vars( $condition, $data );
					$condition  = $this->replace_constants( $condition );
					$condition  = str_replace( array( '&&', '||' ), array( ' AND ', ' OR ' ), $condition );
					$condition  = preg_replace( '/\b(term_id|slug|name)\b/i', 'terms.$1', $condition );

					$attributes = $settings['attributes'];
					$attributes = preg_replace( '/\b(term_id|slug|name)\b/i', 'terms.$1', $attributes );

					$in      = $settings['in'];

					$_from   = 	' FROM ' . $wpdb->terms . ' terms JOIN ' . $wpdb->term_taxonomy . ' term_taxonomy ON (term_taxonomy.term_id=terms.term_id)';

					$_where  = ' WHERE 1=1';

					if ( ! empty( $in ) ) {
						$in  = $this->replace_vars( $in, $data );
						$in  = $this->replace_constants( $in );

						$_from     .= ' JOIN ' . $wpdb->term_relationships . ' term_relationships ON (term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id) ';
						$condition .= ' AND term_relationships.object_id IN (' . $in . ')';
					}

					if ( ! empty( $condition ) ) {
						$_where .= ' AND ' . $condition;
					}

					$_order_by =  strtolower( ! empty( $settings['order_by'] ) ? sanitize_text_field( wp_unslash( $settings['order_by'] ) ) : 'term_id' );
					$_order_by = ' ORDER BY terms.' . ( in_array( $_order_by, ['term_id', 'slug', 'name'] ) ? $_order_by : 'term_id' );

					$query   = 'SELECT ' . $attributes . $_from . $_where . $_order_by;

					$results = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					if ( $wpdb->last_error ) {
						$this->error_log( $wpdb->last_error );
					}
				} else {
					$this->error_log( 'The taxonomy is required' );
				}
			} else {
				$this->error_log( 'The attributes are required' );
			}
			return $results;
		} // End taxonomy_ds

		public function user_ds( $settings, $data ) {
			$results = array();
			$users   = array();
			if ( ! empty( $settings['attributes'] ) ) {
				if (
					( is_numeric( $settings['logged'] ) && 1 == $settings['logged'] ) ||
					( is_string( $settings['logged'] ) && true == $settings['logged'] ) ||
					( is_bool( $settings['logged'] ) && 'true' == $settings['logged'] )
				) {
					$users[] = wp_get_current_user();
				} else {
					global $wpdb;
					$query = 'SELECT ID FROM ' . $wpdb->users . ' WHERE 1=1 ' . ( ! empty( $settings['condition'] ) ? ' AND ' . $settings['condition'] : '' );
					$query = $this->replace_constants( $query );
					$query .=  ' ORDER BY ' . ( ! empty( $settings['order_by'] ) ? sanitize_text_field( wp_unslash( $settings['order_by'] ) ) : 'ID' );
					$rows  = $wpdb->get_results( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					$user  = array();
					foreach ( $rows as $row ) {
						$users[] = get_userdata( $row->ID );
					}
				}

				if ( ! empty( $users ) ) {
					$attributes = explode( ',', $settings['attributes'] );
					foreach ( $users as $user ) {
						$item = array();
						foreach ( $attributes as $attribute ) {
							$attribute = trim( $attribute );
							if ( empty( $attribute ) ) {
								continue;
							}
							$item[ $attribute ] = ! empty( $user->{$attribute} ) ? $user->{$attribute} : '';
						}
						$results[] = $item;
					}
				} else {
					$this->error_log( 'Not user' );
				}
			} else {
				$this->error_log( 'The attributes are required' );
			}

			return $results;
		} // End user_ds

		public function database_ds( $settings, $data ) {
			global $wpdb;

			$query   = '';
			$results = array();

			// Replace parameters on query
			$query = $this->replace_vars( $settings['query'], $data );
			$query = $this->replace_constants( $query );
			if ( ! empty( $query ) ) {
				if (
					! empty( $settings['dns'] ) ||
					! empty( $settings['hostname'] )
				) {
					if ( ! empty( $settings['dns'] ) ) {
						$dns = $settings['dns'];
					} else {
						$settings['engine'] = strtolower( $settings['engine'] );
						switch ( $settings['engine'] ) {
							case 'sqlite':
								$dns = $settings['engine'] . ':' . $settings['hostname'];
								break;
							case 'firebird':
								$dns = $settings['engine'] . ':dbname=' . $settings['database'];
								break;
							case 'ibm':
								$dns = $settings['engine'] . ':DRIVER={IBM DB2 ODBC DRIVER};hostnameNAME=' . $settings['hostname'] . ';PROTOCOL=TCPIP;DATABASE=' . $settings['database'];
								break;
							case 'informix':
								$dns = $settings['engine'] . ':hostname=' . $settings['hostname'] . ';database=' . $settings['database'];
								break;
							case 'sqlsrv':
								$dns = $settings['engine'] . ':Server=' . $settings['hostname'] . ';Database=' . $settings['database'];
								break;
							case 'oci':
								$dns = $settings['engine'] . ':dbname=' . $settings['database'];
								break;
							default:
								$dns = $settings['engine'] . ':charset=utf8mb4;hostname=' . $settings['hostname'] . ';dbname=' . $settings['database'];
								break;
						}
					}

					try {
						if ( ! empty( $dns ) ) {
							$db_connect = new PDO( $dns, $settings['username'], $settings['password'], array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) ); // phpcs:ignore WordPress.DB.RestrictedClasses.mysql__PDO
						}

						$result = $db_connect->query( $query );
						while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ) { // phpcs:ignore WordPress.DB.RestrictedClasses.mysql__PDO
							foreach ( $row as $_key => $_val ) {
								if ( function_exists( 'mb_check_encoding' ) && true !== mb_check_encoding( $_val, 'UTF-8' ) ) {
									$row[ $_key ] = utf8_encode( $_val );
								}
							}
							$results[] = (object) $row;
						}
					} catch ( Exception $err ) {
						$this->error_log( $err->getMessage() );
					}
				} else {
					$results = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					if ( $wpdb->last_error ) {
						$this->error_log( $wpdb->last_error );
					}
				}
			} else {
				$this->error_log( 'The query is empty' );
			}
			return $results;
		} // End database_ds
	} // End class CF7_DATASOURCE_BASIC
}
