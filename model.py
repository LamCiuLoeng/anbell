# -*- coding: utf-8 -*-
'''
###########################################
#  Created on 2014-7-13
#  @author: cl.lam <lamciuloeng@gmail.com>
#  Description:
###########################################
'''
from datetime import datetime as dt
from sqlalchemy import create_engine, Column, ForeignKey, Table
from sqlalchemy.types import Integer, DateTime, Text, Unicode, Float
from sqlalchemy.orm import scoped_session, sessionmaker, relation, backref, synonym
from sqlalchemy.ext.declarative import declarative_base


SQLALCHEMY_DATABASE_URI = "mysql://root:root@127.0.0.1:3306/anbels?charset=utf8"

engine = create_engine( SQLALCHEMY_DATABASE_URI, echo = False, pool_size = 20 )
maker = sessionmaker( bind = engine, autoflush = True, autocommit = False )
DBSession = scoped_session( maker )
DeclarativeBase = declarative_base()
metadata = DeclarativeBase.metadata

# DBSession.configure(bind = engine)

#===============================================================================
# model define
#===============================================================================

class SysMixin( object ):
    create_time = Column( DateTime, default = dt.now )
    create_by_id = Column( Integer, default = 1 )
    update_time = Column( DateTime, default = dt.now, onupdate = dt.now )
    update_by_id = Column( Integer, default = 1 )
    active = Column( Integer, default = 0 )  # 0 is active ,1 is inactive


#===============================================================================
# auth
#===============================================================================
group_permission_table = Table( 'anbels_auth_group_permission', metadata,
    Column( 'group_id', Integer, ForeignKey( 'anbels_auth_group.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'permission_id', Integer, ForeignKey( 'anbels_auth_permission.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True )
 )

user_group_table = Table( 'anbels_auth_user_group', metadata,
    Column( 'user_id', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'group_id', Integer, ForeignKey( 'anbels_auth_group.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True )
 )


class User( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_user'

    id = Column( Integer, autoincrement = True, primary_key = True )
    system_no = Column( Unicode( 20 ), nullable = False )
    name = Column( Unicode( 1000 ) , nullable = False )
    password = Column( Unicode( 1000 ), nullable = False )
    gender = Column( Unicode( 10 ), )
    last_login_time = Column( DateTime )
    salt = Column( Text )



class Group( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_group'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    display_name = Column( Unicode( 1000 ) )
    users = relation( 'User', secondary = user_group_table, backref = 'groups' )


class Permission( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_auth_permission'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )


#===============================================================================
# master
#===============================================================================
class Location( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_location'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    code = Column( Unicode( 1000 ) )
    parent_code = Column( Unicode( 1000 ) )
    full_name = Column( Text )
    full_path_ids = Column( Text )
    parent_id = Column( Integer, default = None )

    def __str__( self ): return self.full_name
    def __repr__( self ): return self.full_name
    def __unicode__( self ): return self.full_name


#
# class Category( DeclarativeBase, SysMixin ):
#     __tablename__ = 'anbels_master_category'
#
#     id = Column( Integer, autoincrement = True, primary_key = True )
#     name = Column( Unicode( 1000 ) )
#     desc = Column( Text )


class Question( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_question'

    id = Column( Integer, autoincrement = True, primary_key = True )
    content = Column( Text )
    correct_answer = Column( Unicode( 10 ), )
    answer01 = Column( Unicode( 10 ), )
    answer01_content = Column( Text )
    answer02 = Column( Unicode( 10 ), )
    answer02_content = Column( Text )
    answer03 = Column( Unicode( 10 ), )
    answer03_content = Column( Text )
    answer04 = Column( Unicode( 10 ), )
    answer04_content = Column( Text )
    answer05 = Column( Unicode( 10 ), )
    answer05_content = Column( Text )
    answer06 = Column( Unicode( 10 ), )
    answer07_content = Column( Text )
    answer08 = Column( Unicode( 10 ), )
    answer08_content = Column( Text )
    answer09 = Column( Unicode( 10 ), )
    answer09_content = Column( Text )
    answer10 = Column( Unicode( 10 ), )
    answer10_content = Column( Text )



class Course( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_course'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
#     category_id = Column( Integer, ForeignKey( 'anbels_master_category.id' ) )
#     category = relation( Category )
    desc = Column( Text )



class Courseware( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_courseware'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    desc = Column( Text )
    path = Column( Unicode( 5000 ) )
    url = Column( Unicode( 5000 ) )



class Game( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_game'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    desc = Column( Text )
    path = Column( Unicode( 5000 ) )
    url = Column( Unicode( 5000 ) )



class School( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_school'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
#     location_id = Column( Integer, ForeignKey( 'anbels_master_location.id' ) )
    location_id = Column( Integer )
    desc = Column( Text )


class_user_table = Table( 'anbels_logic_class_user', metadata,
    Column( 'user_id', Integer, ForeignKey( 'anbels_auth_user.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'class_id', Integer, ForeignKey( 'anbels_master_class.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'role', Unicode( 5 ), )  # T is teacher , S is sudent
 )




class Class( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_master_class'

    id = Column( Integer, autoincrement = True, primary_key = True )
    school_id = Column( Integer, ForeignKey( 'anbels_master_school.id' ) )
    school = relation( School )
    grade = Column( Integer, default = 1 )
    name = Column( Unicode( 1000 ), nullable = False )
    desc = Column( Text )
    users = relation( 'User', secondary = class_user_table, backref = 'class' )

#===============================================================================
# logic
#===============================================================================





# plan_courseware_table = Table( 'anbels_logic_pan_courseware', metadata,
#     Column( 'plan_id', Integer, ForeignKey( 'anbels_logic_plan.id',
#         onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
#     Column( 'courseware_id', Integer, ForeignKey( 'anbels_master_courseware.id',
#         onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
#  )
#
#
# plan_course_table = Table( 'anbels_logic_plan_course', metadata,
#     Column( 'plan_id', Integer, ForeignKey( 'anbels_logic_plan.id',
#         onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
#     Column( 'course_id', Integer, ForeignKey( 'anbels_master_course.id',
#         onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
#  )



class Plan( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_plan'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ), nullable = False )
    school_id = Column( Integer, ForeignKey( 'anbels_master_school.id' ) )
    school = relation( School )
#     grade = Column( Integer, default = 1 )
    desc = Column( Text )
#     courses = relation( 'Course', secondary = plan_course_table, backref = 'plans' )




class PlanCourse( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_plan_course'

    id = Column( Integer, autoincrement = True, primary_key = True )
    plan_id = Column( Integer, ForeignKey( 'anbels_logic_plan.id' ) )
    plan = relation( Plan )
    grade = Column( Integer, default = 1 )
    course_id = Column( Integer, ForeignKey( 'anbels_master_course.id' ) )
    course = relation( Course )
    desc = Column( Text )


course_question_table = Table( 'anbels_logic_course_question', metadata,
    Column( 'course_id', Integer, ForeignKey( 'anbels_master_course.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
    Column( 'qustion_id', Integer, ForeignKey( 'anbels_master_question.id',
        onupdate = "CASCADE", ondelete = "CASCADE" ), primary_key = True ),
 )




# class CourseCourseware( DeclarativeBase, SysMixin ):
#     __tablename__ = 'anbels_logic_pan_courseware'
#
#     id = Column( Integer, autoincrement = True, primary_key = True )
#     course_id = Column( Integer, ForeignKey( 'anbels_logic_course.id' ) )
#     course = relation( Course )
#     category_id = Column( Integer, ForeignKey( 'anbels_master_category.id' ) )
#     obj_id = Column( Integer, ForeignKey( 'anbels_master_courseware.id' ) )
#     obj = relation( Courseware )



# class CourseGame( DeclarativeBase, SysMixin ):
#     __tablename__ = 'anbels_logic_pan_game'
#
#     id = Column( Integer, autoincrement = True, primary_key = True )
#     plan_id = Column( Integer, ForeignKey( 'anbels_logic_plan.id' ) )
#     plan = relation( Plan )
#     category_id = Column( Integer, ForeignKey( 'anbels_master_category.id' ) )
#     obj_id = Column( Integer, ForeignKey( 'anbels_master_game.id' ) )
#     obj = relation( Game )


class StudyLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_logic_study_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    user_id = Column( Integer, ForeignKey( 'anbels_auth_user.id' ) )
    user = relation( User )
    type = Column( Unicode( 5 ), )  # G is game , C is courseware C is course
    refer_id = Column( Integer )
    start_time = Column( DateTime )
    complete_time = Column( DateTime )
    score = Column( Float )
    remark = Column( Text )


#===============================================================================
# system
#===============================================================================
class SystemLog( DeclarativeBase, SysMixin ):
    __tablename__ = 'anbels_system_log'

    id = Column( Integer, autoincrement = True, primary_key = True )
    user_id = Column( Integer )
    remark = Column( Text )


class DataDictionary( DeclarativeBase ):
    __tablename__ = 'anbels_system_data_dictionary'

    id = Column( Integer, autoincrement = True, primary_key = True )
    name = Column( Unicode( 1000 ) )
    value = Column( Text )


#===============================================================================
# for assign the system no value
#===============================================================================
class SystemNo( DeclarativeBase ):
    __tablename__ = 'anbels_system_no'

    system_no = Column( Integer, autoincrement = True, primary_key = True )
    active = Column( Integer, default = 0 )



class CurrentUser( DeclarativeBase ):
    __tablename__ = 'anbels_system_current_user'

    user_key = Column( Unicode( 50 ), primary_key = True )
    time_stamp = Column( DateTime, default = dt.now )



def init():
    print "drop all tables"
    metadata.drop_all( engine, checkfirst = True )
    print "create table"
    metadata.create_all( engine )
    print "insert default value"

    admin = User( system_no = '10000000', name = u'超级管理员' , gender = u'男', password = 'dicJp9R3v8xE2' )
    teacher = User( system_no = '10000001', name = u'老师甲', gender = '女', password = 'dirm.l/sEXGj2' )
    student = User( system_no = '10000002', name = u'学生甲', gender = u'男', password = 'diOP2PAYn8gE6' )
    DBSession.add_all( [ admin, teacher, student] )

    gAdmin = Group( name = 'ADMIN' , display_name = '超级管理员' )
    gAdmin.users = [admin, ]
    gTeacher = Group( name = 'TEACHER' , display_name = '老师' )
    gTeacher.users = [teacher, ]
    gStudent = Group( name = 'STUDENT' , display_name = '学生' )
    gStudent.users = [student, ]
    DBSession.add_all( [gAdmin, gTeacher, gStudent] )

    cr1 = Course( name = u'课程一', )
    cr2 = Course( name = u'课程二' )
    cr3 = Course( name = u'课程三' )
    cr4 = Course( name = u'课程四' )
    cr5 = Course( name = u'课程五' )
    DBSession.add_all( [cr1, cr2, cr3, cr4, cr5, ] )


    cw1 = Courseware( name = u'课件一', course = cr1 )
    cw2 = Courseware( name = u'课件二', course = cr2 )
    cw3 = Courseware( name = u'课件三', course = cr3 )
    cw4 = Courseware( name = u'课件四', course = cr4 )

    cw10 = Courseware( name = u'课件一十', course = cr1 )
    cw20 = Courseware( name = u'课件二十', course = cr2 )
    cw30 = Courseware( name = u'课件三十', course = cr3 )
    cw40 = Courseware( name = u'课件四十', course = cr4 )

    DBSession.add_all( [cw1, cw2, cw3, cw4, cw10, cw20, cw30, cw40, ] )

    #===========================================================================
    # shool
    #===========================================================================
<<<<<<< HEAD
    school = School( name = u'罗湖小学', location_id = 2900 )
    school2 = School( name = u'红岭小学', location_id = 380 )
    school3 = School( name = u'螺岭小学', location_id = 380 )
    school4 = School( name = u'福田实验小学', location_id = 380 )
    school5 = School( name = u'新洲小学', location_id = 380 )
=======
    school = School( name = u'罗湖小学', location_id = 4903 )
    school2 = School( name = u'红岭小学', location_id = 4903 )
    school3 = School( name = u'螺岭小学', location_id = 4903 )
    school4 = School( name = u'福田实验小学', location_id = 4904 )
    school5 = School( name = u'新洲小学', location_id = 4904 )
>>>>>>> c34575895b333bdc14442de5834b7e8166db4309
    DBSession.add_all( [school, school2, school3, school4, school5, ] )
    clz = Class( school = school, grade = 1, name = u'一年级一班' )
    clz2 = Class( school = school2, grade = 2, name = u'二年级二班' )
    clz3 = Class( school = school3, grade = 3, name = u'三年级三班' )
    clz4 = Class( school = school4, grade = 4, name = u'四年级四班' )
    DBSession.add_all( [clz, clz2, clz3, clz4, ] )
    clz.users = [student, ]


    plan1 = Plan( name = u'罗湖小学教堂计划', school = school )
    pc1 = PlanCourse( plan = plan1, grade = 1, course = cr1 )

    plan2 = Plan( name = u'红岭小学教堂计划', school = school2 )
    pc2 = PlanCourse( plan = plan2, grade = 1, course = cr2 )

    DBSession.add_all( [plan1, plan2, pc1, pc2 ] )

    DBSession.commit()
    print "Done"


if __name__ == "__main__":
    init()
