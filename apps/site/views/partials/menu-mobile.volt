<div id="side-panel" class="dark">

    <div id="side-panel-trigger-close" class="side-panel-trigger"><a href="#"><i class="icon-line-cross"></i></a></div>

    <div class="side-panel-wrap">

        <div class="widget clearfix">

            <h4>Menu</h4>

            <nav class="nav-tree nobottommargin">
                <ul>
                    {% set menu = tags.renderBlock('menu', false)|json_decode %}
                    {% for item in menu %}
                    <li
                        {% if item.submenu is not null %}
                        class="sub-menu"
                        {% endif %}
                    >
                        {% if item.blockTplMobile is defined %}
                        {{tags.renderBlock(item.blockTplMobile)}}
                        {% else %}
                        <a href="{{item.href}}">
                            <i class="fa fa-chevron-right"></i>
                            {{item.title}}
                        </a>
                        {% if item.submenu is not null %}
                        <ul>
                            {% for subitem in item.submenu %}
                            <li
                                {% if subitem.class is not null %}
                                class="{{subitem.class}}"
                                {% endif %}
                            >
                                <a href="{{subitem.href}}">
                                    {{subitem.title}}
                                </a>
                            </li>
                            {% endfor %}
                        </ul>
                        {% endif %}
                        {% endif %}
                    </li>
                    {% endfor %}
                </ul>
            </nav>

        </div>

    </div>

</div>