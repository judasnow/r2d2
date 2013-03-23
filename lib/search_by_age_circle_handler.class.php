
                //按身高搜索的流程
                if( $circle == 'search_by_height' ) {
                        if( $request_content > 140 && $request_content < 250 ) {
                                //调用 api 使用身高 目标性别 以及当前的地址信息进行查询
                                $location = $this->_context->get( 'location' );
                                $target_sex = $this->_context->get( 'target_sex' );
                                $height = $request_content;

                                //注意 web 版本上的奇葩变量名称
                                $res_json = $this->_api->search( array( 'height' => $height , 'location' => $location , 'sex' => $target_sex ) );
                                $res = json_decode( $res_json , true );

                                if( $res['type'] == 'success' ) {
                                        $user_infos = json_decode( $res['info'] , true );
                                        //获取当前 cursor
                                        $cursor = $this->_context->get( 'search_by_height_cursor' );
                                        if( empty( $cursor ) || $cursor >= count( $user_infos ) ) {
                                                $cursor = 0;
                                        }
                                        //作为结果返回
                                        $search_result_user_info = $user_infos[$cursor];
                                        $cursor++;

                                        $items = array(
                                                array(
                                                        'title'=>$search_result_user_info['NickName'] ,
                                                        'description'=>$search_result_user_info['ZWMS'] ,
                                                        'pic_url'=>'http://huaban123.com/UploadFiles/UHP/MIN/' . $search_result_user_info['HeadPic'] , 
                                                        'url'=>'http://www.huaban123.com/Action/WeixinUserInfoDetail.aspx?user_id=' . $search_result_user_info['UserId']
                                                )
                                        );
                                        $this->_response = $this->_msg_producer->do_produce( 
                                                'news' , 
                                                array( 'items' => $items )
                                        );
                                        return $this->_response;
                                }
                        }
