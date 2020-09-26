@spaceless()
@php $base_url = get_site_url(); @endphp

@switch($post_type)

    @case('dataset')

        <div class="people dataset_people">
            <table>
                <thead>
                <tr><th>Name</th><th>Affiliation</th><th>Contact</th></tr>
                </thead>
                <tbody>
                @foreach($field_rows as $i => $row)
                    @php
                        $id = $row['person']->ID;
                        $name = $row['person']->post_title;
                        $affiliation = $row['affiliation'];
                        $contact = $row['contact'] ? '✓' : ''
                    @endphp
                    <tr>
                        <td><a href="{{$base_url}}/?s=&person_id={{$id}}">{{$name}}</a></td>
                        <td>{{$affiliation}}</td>
                        <td>{{$contact}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @break

    @case('data_project')

        <div class="people project_people">
            <table>
                <thead>
                <tr><th>Name</th><th>Affiliation</th><th>Role</th></tr>
                </thead>
                <tbody>
                @foreach($field_rows as $i => $row)
                    @php
                        $id = $row['person']->ID;
                        $name = $row['person']->post_title;
                        $affiliation = $row['affiliation'];
                        $role = $row['role'];
                    @endphp
                    <tr>
                        <td><a href="{{$base_url}}/?s=&person_id={{$id}}">{{$name}}</a></td>
                        <td>{{$affiliation}}</td>
                        <td>{{$role}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @break

    @default
        @break

@endswitch

@endspaceless()