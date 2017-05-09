<body>
    <div class='container'>
        <div class='row'>
            <div class='col-xs-12'>

                <h1 class='text-center'>List of Composer packages and their status</h1>

                <hr/>

                <table class='table table-striped'>
                    <thead>
                        <th>Name</th>
                        <th nowrap class='text-right'>Version installed</th>
                        <th nowrap class='text-right'>Latest version</th>
                        <th class='text-center'>Status</th>
                        <th><span class='hidden-xs'>Description</span></th>
                    </thead>
                    <tbody>
                    {% for package in packages %}
                        <tr>
                            <td nowrap>
                                {% if package.homepage %}
                                    <a href='{{ package.homepage|e }}' target="_blank" rel='noopener'>{{ package.name|e }}</a>
                                {% else %}
                                    {{ package.name|e }}
                                {% endif %}
                            </td>
                            <td class='text-right'>{{ package.version|e }}</td>
                            <td class='text-right'>{{ package.latest|e }}</td>
                            <td class='text-center'>
                                <div class='status status-{{ package.status|e }}'></div>
                            </td>
                            <td><span class='hidden-xs'>{{ package.description|e }}</span></td>
                        </tr>
                        {% if package.warning %}
                            <tr>
                                <td class='warning' colspan='5'>{{ package.warning|e }}</td>
                            </tr>
                        {% endif %}
                    {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
